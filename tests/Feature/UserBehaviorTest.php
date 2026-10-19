<?php

use App\Models\Business;
use App\Models\Company;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

// --- World ---

function userWorld(): array
{
    Storage::fake('public');
    $pdo = DB::connection()->getPdo();
    $pdo->sqliteCreateFunction('REGEXP', fn ($pattern, $value) => (int) preg_match('/'.$pattern.'/', (string) $value), 2);

    $admin = User::factory()->admin()->create(['name' => 'Admin Person']);
    $student = User::factory()->create(['name' => 'Student Person', 'is_visible' => true]);

    return compact('admin', 'student');
}

function userPayload(array $overrides = []): array
{
    return $overrides + [
        'name' => 'New Person',
        'email' => 'new.person@example.com',
        'password' => 'Secret-Password-123',
        'password_confirmation' => 'Secret-Password-123',
        'role' => 'user',
        'student_status' => 'alumni',
        'major' => 'Management',
        'current_status' => 'Entrepreneur',
        'is_visible' => 1,
    ];
}

// --- Admin list ---

test('admin user list hides admins, filters and counts', function () {
    $w = userWorld();
    User::factory()->create(['name' => 'Ent One', 'current_status' => 'Entrepreneur', 'major' => 'Bisnis', 'year_of_enrollment' => '2021']);
    User::factory()->create(['name' => 'Intra One', 'current_status' => 'intrapreneur', 'student_status' => 'alumni', 'is_featured' => true]);

    $response = $this->actingAs($w['admin'])->get(route('users.index'))->assertOk()->assertViewIs('users.index');
    expect($response->viewData('users')->pluck('name')->all())->not->toContain('Admin Person')
        ->and([$response->viewData('totalUsers'), $response->viewData('totalEntrepreneurs'), $response->viewData('totalIntrapreneurs'),
            $response->viewData('totalAlumni'), $response->viewData('featuredUserCount'), $response->viewData('featuredIntrapreneurCount')])
        ->toBe([3, 1, 1, 1, 1, 1]);

    $names = fn ($query) => $this->actingAs($w['admin'])->get(route('users.index').$query)->viewData('users')->pluck('name')->all();
    expect($names('?search=Intra'))->toBe(['Intra One'])
        ->and($names('?current_status=Entrepreneur'))->toBe(['Ent One'])
        ->and($names('?major=Bisnis'))->toBe(['Ent One'])
        ->and($names('?student_status=alumni'))->toBe(['Intra One'])
        ->and($names('?sort_name=asc'))->toBe(['Ent One', 'Intra One', 'Student Person']);

    $this->actingAs($w['admin'])->get(route('users.index'), ['X-Requested-With' => 'XMLHttpRequest'])->assertViewIs('users.partials.list');
    $this->actingAs($w['student'])->get(route('users.index'))->assertForbidden();
});

test('stats answer json counts', function () {
    $w = userWorld();
    User::factory()->create(['current_status' => 'Entrepreneur', 'is_featured' => true]);

    $this->actingAs($w['admin'])->get(route('users.stats'))->assertExactJson([
        'total' => 2, 'entrepreneurs' => 1, 'intrapreneurs' => 0, 'alumni' => 0,
        'featured' => 1, 'featured_intrapreneurs' => 0, 'featured_entrepreneurs' => 1,
    ]);
});

// --- Create and update ---

test('admin creates a user with a hashed password and assigned businesses', function () {
    $w = userWorld();
    $business = Business::create(['user_id' => $w['student']->id, 'name' => 'Handed Over']);

    $this->actingAs($w['admin'])->get(route('users.create'))->assertOk()->assertViewHas(['availableBusinesses', 'provinces']);
    $this->actingAs($w['admin'])->post(route('users.store'), userPayload(['owned_businesses' => [$business->id]]))
        ->assertRedirect(route('users.index'))
        ->assertSessionHas('success', "Success! The profile for 'New Person' has been created.");

    $user = User::where('email', 'new.person@example.com')->firstOrFail();
    expect(Hash::check('Secret-Password-123', $user->password))->toBeTrue()
        ->and($user->student_status)->toBe('alumni')
        ->and($user->is_visible)->toBeTrue()
        ->and($user->email_verified_at)->not->toBeNull()
        ->and($user->slug)->toBe('new-person')
        ->and($business->fresh()->user_id)->toBe($user->id);
});

test('an admin account is stored with the default student status, validation still applies', function () {
    $w = userWorld();

    $this->actingAs($w['admin'])->post(route('users.store'), userPayload(['email' => 'boss@example.com', 'role' => 'admin', 'student_status' => null]));
    expect(User::where('email', 'boss@example.com')->value('student_status'))->toBe('student aktif');

    $this->actingAs($w['admin'])->post(route('users.store'), userPayload(['email' => $w['student']->email, 'password_confirmation' => 'other']))
        ->assertSessionHasErrors(['email', 'password']);
    $this->actingAs($w['student'])->post(route('users.store'), userPayload())->assertForbidden();
});

test('a profile photo upload is stored under /storage/profile-photos', function () {
    $w = userWorld();

    $this->actingAs($w['admin'])->post(route('users.store'), userPayload(['profile_photo_url' => UploadedFile::fake()->image('me.jpg')]));
    $path = User::where('email', 'new.person@example.com')->firstOrFail()->getRawOriginal('profile_photo_url');
    expect($path)->toStartWith('/storage/profile-photos/new-person_');
});

test('update keeps the password unless given and moves business ownership', function () {
    $w = userWorld();
    $target = User::factory()->create(['name' => 'Target', 'email' => 'target@example.com']);
    $oldHash = $target->password;
    $kept = Business::create(['user_id' => $target->id, 'name' => 'Kept']);
    $dropped = Business::create(['user_id' => $target->id, 'name' => 'Dropped']);
    $gained = Business::create(['user_id' => $w['student']->id, 'name' => 'Gained']);

    $this->actingAs($w['admin'])->get(route('users.edit', $target))->assertOk()
        ->assertViewHas('userToEdit', fn ($u) => $u->is($target))
        ->assertViewHas('ownedBusinessIds', [$kept->id, $dropped->id]);

    $this->actingAs($w['admin'])->put(route('users.update', $target), userPayload([
        'name' => 'Target Renamed', 'email' => 'target@example.com', 'password' => null, 'password_confirmation' => null,
        'owned_businesses' => [$kept->id, $dropped->id, $gained->id],
    ]))->assertRedirect(route('users.show', $target))
        ->assertSessionHas('success', "Success! The profile for 'Target Renamed' has been updated.");

    $target->refresh();
    expect($target->password)->toBe($oldHash)
        ->and($target->slug)->toBe('target')
        ->and($kept->fresh()->user_id)->toBe($target->id)
        ->and($gained->fresh()->user_id)->toBe($target->id)
        ->and($dropped->fresh()->user_id)->toBe($target->id);

    $this->actingAs($w['admin'])->put(route('users.update', $target), userPayload([
        'email' => 'target@example.com', 'password' => null, 'password_confirmation' => null, 'owned_businesses' => [$gained->id],
    ]));
    expect($dropped->fresh()->user_id)->toBe($target->id);

    $this->actingAs($w['admin'])->put(route('users.update', $target), userPayload(['email' => 'target@example.com', 'password' => 'Another-Secret-456', 'password_confirmation' => 'Another-Secret-456']));
    expect(Hash::check('Another-Secret-456', $target->fresh()->password))->toBeTrue();
});

test('assigning a business to a user who already owns one keeps both', function () {
    $w = userWorld();
    $target = User::factory()->create(['email' => 'owner@example.com']);
    $existing = Business::create(['user_id' => $target->id, 'name' => 'Already Theirs']);
    $incoming = Business::create(['user_id' => $w['student']->id, 'name' => 'Moved Over']);

    $this->actingAs($w['admin'])->put(route('users.update', $target), userPayload([
        'email' => 'owner@example.com', 'password' => null, 'password_confirmation' => null, 'owned_businesses' => [$incoming->id],
    ]))->assertRedirect(route('users.show', $target));

    expect($existing->fresh()->user_id)->toBe($target->id)
        ->and($incoming->fresh()->user_id)->toBe($target->id);
});

test('saving the admin edit form keeps visibility and contact settings unless they are sent', function () {
    $w = userWorld();
    $target = User::factory()->create(['email' => 'visible@example.com', 'is_visible' => true, 'show_contact_details' => true]);
    $payload = userPayload(['email' => 'visible@example.com', 'password' => null, 'password_confirmation' => null]);
    unset($payload['is_visible']);

    $this->actingAs($w['admin'])->put(route('users.update', $target), $payload)->assertSessionHasNoErrors();
    expect($target->fresh()->is_visible)->toBeTrue()->and($target->fresh()->show_contact_details)->toBeTrue();

    $this->actingAs($w['admin'])->put(route('users.update', $target), $payload + ['is_visible' => 0, 'show_contact_details' => 0]);
    expect($target->fresh()->is_visible)->toBeFalse()->and($target->fresh()->show_contact_details)->toBeFalse();
});

test('a user created by an admin is visible unless told otherwise', function () {
    $w = userWorld();
    $payload = userPayload(['email' => 'fresh@example.com']);
    unset($payload['is_visible']);

    $this->actingAs($w['admin'])->post(route('users.store'), $payload);
    expect(User::where('email', 'fresh@example.com')->value('is_visible'))->toBeTrue();

    $this->actingAs($w['admin'])->post(route('users.store'), userPayload(['email' => 'hidden@example.com', 'is_visible' => 0]));
    expect(User::where('email', 'hidden@example.com')->value('is_visible'))->toBeFalse();
});

test('update can add and remove activity documents', function () {
    $w = userWorld();
    $target = User::factory()->create(['email' => 'docs@example.com', 'activities_doc_url' => ['/storage/student-activities/old.pdf', '/storage/student-activities/keep.pdf']]);

    $this->actingAs($w['admin'])->put(route('users.update', $target), userPayload([
        'email' => 'docs@example.com', 'password' => null, 'password_confirmation' => null,
        'delete_activities_files' => ['/storage/student-activities/old.pdf'],
        'activities_docs' => [UploadedFile::fake()->create('new.pdf', 10, 'application/pdf')],
    ]))->assertSessionHasNoErrors();

    $docs = $target->fresh()->activities_doc_url;
    expect($docs)->toHaveCount(2)
        ->and($docs[0])->toBe('/storage/student-activities/keep.pdf')
        ->and($docs[1])->toStartWith('/storage/student-activities/act_');
});

// --- Toggles ---

test('featured toggle refuses hidden users and answers json', function () {
    $w = userWorld();
    $hidden = User::factory()->create(['is_visible' => false]);

    $this->actingAs($w['admin'])->postJson(route('users.toggle-featured', $hidden))->assertStatus(422)->assertJson(['success' => false]);
    $this->actingAs($w['admin'])->postJson(route('users.toggle-featured', $w['student']))
        ->assertJson(['success' => true, 'is_featured' => true, 'message' => "User 'Student Person' has been added to featured list."]);
    $this->actingAs($w['admin'])->post(route('users.toggle-featured', $w['student']))
        ->assertSessionHas('success', "User 'Student Person' has been removed from featured list.");
    $this->actingAs($w['student'])->postJson(route('users.toggle-featured', $w['student']))->assertForbidden();
});

test('deactivating a user unfeatures them and hides their own businesses and work profiles', function () {
    $w = userWorld();
    $w['student']->update(['is_featured' => true]);
    $business = Business::create(['user_id' => $w['student']->id, 'name' => 'Theirs', 'is_visible' => true]);
    $business->approval_status = 'approved';
    $business->save();
    $company = Company::create(['user_id' => $w['student']->id, 'name' => 'Their Employer', 'is_visible' => true]);

    $this->actingAs($w['admin'])->postJson(route('users.toggle-status', $w['student']))
        ->assertJson(['success' => true, 'is_visible' => false, 'is_featured' => false]);
    expect(Business::visible()->whereKey($business->id)->exists())->toBeFalse()
        ->and($company->fresh()->is_visible)->toBeFalse();

    $this->actingAs($w['admin'])->post(route('users.toggle-status', $w['student']))
        ->assertSessionHas('success', "User 'Student Person' has been activated successfully.");
    expect(Business::visible()->whereKey($business->id)->exists())->toBeTrue()
        ->and($company->fresh()->is_visible)->toBeTrue();
});

test('re-activating a user restores businesses hidden by an earlier deactivation', function () {
    $w = userWorld();
    $w['student']->update(['is_visible' => false]);
    $business = Business::create(['user_id' => $w['student']->id, 'name' => 'Hidden Earlier', 'is_visible' => false]);

    $this->actingAs($w['admin'])->post(route('users.toggle-status', $w['student']));
    expect($business->fresh()->is_visible)->toBeTrue();
});

// --- Import ---

test('user import needs a file and queues the importer', function () {
    $w = userWorld();
    Excel::fake();

    $this->actingAs($w['admin'])->post(route('users.import'), [])->assertSessionHasErrors('file');

    $file = UploadedFile::fake()->createWithContent('entrepreneur.csv', "Timestamp,Full Name\n2026-01-01,Test\n");
    $this->actingAs($w['admin'])->postJson(route('users.import'), ['file' => $file])
        ->assertOk()->assertJson(['success' => true, 'format' => 'Form Response'])
        ->assertSessionHas('active_user_import_id');
});

// --- Public profile ---

test('a profile opens by slug or by id and lists only approved, visible businesses', function () {
    $w = userWorld();
    Business::create(['user_id' => $w['student']->id, 'name' => 'Shown Biz', 'is_visible' => true, 'approval_status' => 'approved']);
    Business::create(['user_id' => $w['student']->id, 'name' => 'Hidden Biz', 'is_visible' => false, 'approval_status' => 'approved']);
    Business::create(['user_id' => $w['student']->id, 'name' => 'Waiting Biz', 'is_visible' => true, 'approval_status' => 'pending']);

    $response = $this->get('/'.$w['student']->slug)->assertOk()->assertViewIs('users.show');
    expect($response->viewData('user')->businesses->pluck('name')->all())->toBe(['Shown Biz']);

    $this->get('/'.$w['student']->id)->assertOk();
    $this->get('/nobody-here')->assertNotFound();
});

test('the google drive helper redirects to drive', function () {
    $this->get('/google-drive-image/abc123')->assertRedirect('https://drive.google.com/uc?export=view&id=abc123');
});

// --- Profile form data ---

test('activity files describe each stored document with a preview when one exists', function () {
    $user = User::factory()->create([
        'name' => 'Budi Santoso',
        'activities_doc_url' => 'https://drive.google.com/file/d/1AbCdEfGhIjKlMnOpQrStUvWxYz012345/view;/storage/student-activities/act_1.png;/storage/student-activities/report.pdf',
    ]);

    expect($user->activity_files)->toBe([
        ['url' => 'https://drive.google.com/file/d/1AbCdEfGhIjKlMnOpQrStUvWxYz012345/view', 'previewUrl' => 'https://drive.google.com/thumbnail?id=1AbCdEfGhIjKlMnOpQrStUvWxYz012345&sz=w400', 'name' => 'view'],
        ['url' => '/storage/student-activities/act_1.png', 'previewUrl' => '/storage/student-activities/act_1.png', 'name' => 'act_1.png'],
        ['url' => '/storage/student-activities/report.pdf', 'previewUrl' => null, 'name' => 'report.pdf'],
    ])
        ->and($user->initials)->toBe('BS')
        ->and(User::factory()->create(['activities_doc_url' => null])->activity_files)->toBe([]);
});

test('profile page data: gallery links, certificate previews, role label and photo checks', function () {
    $user = User::factory()->create([
        'role' => 'user',
        'activities_doc_url' => 'https://drive.google.com/file/d/1AbCdEfGhIjKlMnOpQrStUvWxYz012345/view;/storage/a.png',
        'expertise_certification_url' => 'https://drive.google.com/open?id=1AbCdEfGhIjKlMnOpQrStUvWxYz012345, https://res.cloudinary.com/x/cert.pdf; /storage/c.png; /files/d.PDF; https://example.com/e',
    ]);

    expect($user->activity_gallery_urls)->toBe(['https://drive.google.com/uc?export=view&id=1AbCdEfGhIjKlMnOpQrStUvWxYz012345', '/storage/a.png'])
        ->and(array_map(fn ($file) => [$file['previewUrl'], $file['isPdf'], $file['isImage'], $file['isGoogleDrive']], $user->certification_files))->toBe([
            ['https://drive.google.com/file/d/1AbCdEfGhIjKlMnOpQrStUvWxYz012345/preview', true, false, true],
            ['https://res.cloudinary.com/x/cert.jpg', false, true, false],
            ['/storage/c.png', false, true, false],
            ['/files/d.PDF', true, false, false],
            ['https://example.com/e', false, true, false],
        ])
        ->and($user->role_label)->toBe('User')
        ->and($user->has_profile_photo)->toBeFalse()
        ->and(User::factory()->create(['profile_photo_url' => 'https://example.com/p.png'])->has_profile_photo)->toBeTrue();
});
