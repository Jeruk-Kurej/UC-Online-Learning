<?php

use App\Models\Business;
use App\Models\Category;
use App\Models\Collab;
use App\Models\Company;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

// --- World ---

function businessWorld(): array
{
    Storage::fake('public');
    // The IndoRegion migrations index `id` instead of making it a primary key, which SQLite
    // refuses as a foreign-key target. Recreate the two tables with primary keys for this test.
    foreach (['villages', 'districts', 'regencies', 'provinces'] as $table) {
        Schema::dropIfExists($table);
    }
    Schema::create('provinces', fn ($t) => [$t->char('id', 2)->primary(), $t->string('name')]);
    Schema::create('regencies', fn ($t) => [$t->char('id', 4)->primary(), $t->char('province_id', 2), $t->string('name')]);

    DB::table('provinces')->insert(['id' => '35', 'name' => 'JAWA TIMUR']);
    DB::table('regencies')->insert([
        ['id' => '3578', 'province_id' => '35', 'name' => 'KOTA SURABAYA'],
        ['id' => '3573', 'province_id' => '35', 'name' => 'KOTA MALANG'],
    ]);

    $category = Category::create(['name' => 'Kuliner', 'slug' => 'kuliner']);
    $other = Category::create(['name' => 'Jasa', 'slug' => 'jasa']);
    $admin = User::factory()->create(['role' => 'admin']);
    $owner = User::factory()->create(['name' => 'Owner Person']);
    $stranger = User::factory()->create(['name' => 'Stranger Person']);

    return compact('category', 'other', 'admin', 'owner', 'stranger');
}

function businessPayload(Category $category, array $overrides = []): array
{
    return $overrides + [
        'name' => 'Warung Baru',
        'description' => 'Warung makan keluarga.',
        'category_id' => $category->id,
        'business_mode' => 'both',
        'phone' => '0311234',
        'whatsapp_number' => '0812000',
        'instagram_handle' => '@warung',
        'province' => '35',
        'city' => '3578',
    ];
}

function approvedBusiness(User $owner, Category $category, array $overrides = []): Business
{
    $business = new Business($overrides + [
        'user_id' => $owner->id,
        'category_id' => $category->id,
        'name' => 'Approved Venture',
        'is_visible' => true,
        'type' => 'entrepreneur',
    ]);
    $business->approval_status = $overrides['approval_status'] ?? 'approved';
    $business->save();

    return $business;
}

// --- Store and update ---

test('a student business is stored hidden with mapped fields and resolved location names', function () {
    $w = businessWorld();

    $this->actingAs($w['owner'])->post(route('businesses.store'), businessPayload($w['category'], ['owner_ids' => [$w['stranger']->id]]))
        ->assertRedirect(route('businesses.my'))
        ->assertSessionHas('success', 'Business submitted for approval! An admin will review it soon.');

    $business = Business::where('name', 'Warung Baru')->firstOrFail();
    expect($business->is_visible)->toBeFalse()
        ->and($business->user_id)->toBe($w['owner']->id)
        ->and($business->type)->toBe('entrepreneur')
        ->and($business->offering_type)->toBe('both')
        ->and($business->phone_number)->toBe('0311234')
        ->and($business->whatsapp)->toBe('0812000')
        ->and($business->instagram)->toBe('@warung')
        ->and($business->province)->toBe('JAWA TIMUR')
        ->and($business->city)->toBe('KOTA SURABAYA')
        ->and($business->members()->pluck('users.id')->all())->toBe([$w['stranger']->id]);
});

test('an admin business is stored visible with the admin message', function () {
    $w = businessWorld();

    $this->actingAs($w['admin'])->post(route('businesses.store'), businessPayload($w['category'], ['name' => 'Admin Venture']))
        ->assertSessionHas('success', 'Business created successfully!');

    expect(Business::where('name', 'Admin Venture')->value('is_visible'))->toBeTrue();
});

test('a logo upload is stored on the public disk', function () {
    $w = businessWorld();

    $this->actingAs($w['owner'])->post(route('businesses.store'), businessPayload($w['category'], ['logo' => UploadedFile::fake()->image('logo.png')]));

    $path = Business::where('name', 'Warung Baru')->firstOrFail()->getRawOriginal('logo_url');
    expect($path)->toStartWith('logos/');
    Storage::disk('public')->assertExists($path);
});

test('an owner update cannot change featured, owner or visibility; an admin update can', function () {
    $w = businessWorld();
    $business = approvedBusiness($w['owner'], $w['category']);

    $this->actingAs($w['owner'])->put(route('businesses.update', $business), businessPayload($w['other'], [
        'name' => 'Renamed Venture', 'user_id' => $w['stranger']->id, 'is_featured' => 1,
    ]))->assertRedirect(route('businesses.show', $business))->assertSessionHas('success', 'Business updated successfully!');

    $business->refresh();
    expect($business->name)->toBe('Renamed Venture')
        ->and($business->slug)->toBe('approved-venture')
        ->and($business->user_id)->toBe($w['owner']->id)
        ->and($business->is_featured)->toBeFalse()
        ->and($business->category_id)->toBe($w['other']->id);

    $this->actingAs($w['admin'])->put(route('businesses.update', $business), businessPayload($w['other'], [
        'name' => 'Renamed Venture', 'user_id' => $w['stranger']->id, 'is_featured' => 1,
    ]));
    $business->refresh();
    expect($business->user_id)->toBe($w['stranger']->id)->and($business->is_featured)->toBeTrue();
});

test('saving the edit form keeps a business featured', function () {
    $w = businessWorld();
    $business = approvedBusiness($w['owner'], $w['category'], ['is_featured' => true]);

    $this->actingAs($w['owner'])->put(route('businesses.update', $business), businessPayload($w['category'], ['name' => 'Owner Edit']));
    expect($business->fresh()->is_featured)->toBeTrue();

    $this->actingAs($w['admin'])->put(route('businesses.update', $business), businessPayload($w['category'], ['name' => 'Admin Edit']));
    expect($business->fresh()->is_featured)->toBeTrue();

    $this->actingAs($w['admin'])->put(route('businesses.update', $business), businessPayload($w['category'], ['name' => 'Admin Unfeature', 'is_featured' => 0]));
    expect($business->fresh()->is_featured)->toBeFalse();
});

test('update syncs members and can delete the logo', function () {
    $w = businessWorld();
    $business = approvedBusiness($w['owner'], $w['category'], ['logo_url' => 'logos/old.png']);
    $business->members()->attach($w['admin']->id);

    $this->actingAs($w['owner'])->put(route('businesses.update', $business), businessPayload($w['category'], [
        'owner_ids' => [$w['stranger']->id], 'delete_logo' => 1,
    ]));

    $business->refresh();
    expect($business->members()->pluck('users.id')->all())->toBe([$w['stranger']->id])
        ->and($business->getRawOriginal('logo_url'))->toBeNull();
});

test('a stranger cannot open the edit form or update', function () {
    $w = businessWorld();
    $business = approvedBusiness($w['owner'], $w['category']);

    $this->actingAs($w['stranger'])->get(route('businesses.edit', $business))->assertForbidden();
    $this->actingAs($w['stranger'])->put(route('businesses.update', $business), businessPayload($w['category']))->assertForbidden();
});

test('the edit form receives the cities of the saved province', function () {
    $w = businessWorld();
    $business = approvedBusiness($w['owner'], $w['category'], ['province' => 'JAWA TIMUR']);

    $this->actingAs($w['owner'])->get(route('businesses.edit', $business))
        ->assertOk()
        ->assertViewHas('selectedProvinceId', '35')
        ->assertViewHas('availableCities', fn ($cities) => $cities->pluck('name')->all() === ['KOTA MALANG', 'KOTA SURABAYA']);
});

// --- Public pages ---

test('a hidden business is only shown to owner, member, collab partner and admin', function () {
    $w = businessWorld();
    $business = approvedBusiness($w['owner'], $w['category'], ['approval_status' => 'pending', 'is_visible' => false]);
    $member = User::factory()->create();
    $business->members()->attach($member->id);
    $partner = User::factory()->create();
    Collab::create(['sender_id' => $partner->id, 'recipient_id' => $w['owner']->id, 'status' => 'accepted']);

    $this->get(route('businesses.show', $business))->assertNotFound();
    $this->actingAs($w['stranger'])->get(route('businesses.show', $business))->assertNotFound();
    $this->actingAs($w['owner'])->get(route('businesses.show', $business))->assertOk();
    $this->actingAs($member)->get(route('businesses.show', $business))->assertOk();
    $this->actingAs($partner)->get(route('businesses.show', $business))->assertOk();
    $this->actingAs($w['admin'])->get(route('businesses.show', $business))->assertOk();
});

test('the showcase url resolves businesses first, then work profiles', function () {
    $w = businessWorld();
    $business = approvedBusiness($w['owner'], $w['category'], ['name' => 'Shared Name']);
    $company = Company::create(['user_id' => $w['owner']->id, 'name' => 'Employer Only', 'is_visible' => true, 'approval_status' => 'approved']);

    $this->get('/business/'.$business->slug)->assertOk()->assertViewIs('businesses.show');
    $this->get('/business/'.$company->slug)->assertOk()->assertViewIs('businesses.show_intrapreneur');
    $this->get('/business/nothing-here')->assertNotFound();
});

test('a hidden work profile is only shown to owner, collab partner and admin', function () {
    $w = businessWorld();
    $company = Company::create(['user_id' => $w['owner']->id, 'name' => 'Quiet Employer', 'is_visible' => false]);
    $partner = User::factory()->create();
    Collab::create(['sender_id' => $w['owner']->id, 'recipient_id' => $partner->id, 'status' => 'accepted']);

    $this->get('/business/'.$company->slug)->assertNotFound();
    $this->actingAs($w['stranger'])->get('/business/'.$company->slug)->assertNotFound();
    $this->actingAs($w['owner'])->get('/business/'.$company->slug)->assertOk();
    $this->actingAs($partner)->get('/business/'.$company->slug)->assertOk();
    $this->actingAs($w['admin'])->get('/business/'.$company->slug)->assertOk();
});

test('the directory filters by search, category and province and serves a partial over ajax', function () {
    $w = businessWorld();
    approvedBusiness($w['owner'], $w['category'], ['name' => 'Kopi Pagi', 'province' => 'Jawa Timur', 'city' => 'Surabaya']);
    approvedBusiness($w['owner'], $w['other'], ['name' => 'Jasa Desain', 'province' => 'Bali', 'city' => 'Denpasar']);
    approvedBusiness($w['owner'], $w['category'], ['name' => 'Belum Disetujui', 'approval_status' => 'pending']);

    $names = fn ($response) => $response->viewData('businesses')->pluck('name')->all();

    expect($names($this->get('/business')))->toEqualCanonicalizing(['Kopi Pagi', 'Jasa Desain'])
        ->and($names($this->get('/business?search=Kopi')))->toBe(['Kopi Pagi'])
        ->and($names($this->get('/business?category='.$w['other']->id)))->toBe(['Jasa Desain'])
        ->and($names($this->get('/business?province=Bali')))->toBe(['Jasa Desain']);

    $this->get('/business', ['X-Requested-With' => 'XMLHttpRequest'])->assertOk()->assertViewIs('businesses.partials.list');
});

test('the intrapreneur directory lists approved work profiles', function () {
    $w = businessWorld();
    Company::create(['user_id' => $w['owner']->id, 'name' => 'Listed Employer', 'is_visible' => true, 'approval_status' => 'approved']);
    Company::create(['user_id' => $w['owner']->id, 'name' => 'Pending Employer', 'is_visible' => true, 'approval_status' => 'pending']);

    $response = $this->get('/business?view=intrapreneur');
    expect($response->viewData('businesses')->pluck('name')->all())->toBe(['Listed Employer'])
        ->and($response->viewData('viewType'))->toBe('intrapreneur');
});

test('my page lists the user businesses and work profiles', function () {
    $w = businessWorld();
    approvedBusiness($w['owner'], $w['category'], ['name' => 'Mine']);
    approvedBusiness($w['stranger'], $w['category'], ['name' => 'Not Mine']);
    Company::create(['user_id' => $w['owner']->id, 'name' => 'My Employer']);

    $response = $this->actingAs($w['owner'])->get(route('businesses.my'))->assertOk();
    expect($response->viewData('myBusinesses')->pluck('name')->all())->toBe(['Mine'])
        ->and($response->viewData('myCompanies')->pluck('name')->all())->toBe(['My Employer']);
});

test('regencies are returned title-cased for a province', function () {
    $w = businessWorld();

    $this->actingAs($w['owner'])->get('/api/regencies')->assertExactJson([]);
    $this->actingAs($w['owner'])->get('/api/regencies?province_id=35')->assertExactJson([
        ['id' => 3573, 'name' => 'Kota Malang'],
        ['id' => 3578, 'name' => 'Kota Surabaya'],
    ]);
});

// --- Admin ---

test('admin status update sets approval, reason and visibility', function () {
    $w = businessWorld();
    $business = approvedBusiness($w['owner'], $w['category']);

    $this->actingAs($w['admin'])->post(route('businesses.update-status', $business), ['status' => 'need_revision'])
        ->assertSessionHasErrors('rejection_reason');
    $this->actingAs($w['admin'])->post(route('businesses.update-status', $business), ['status' => 'need_revision', 'rejection_reason' => 'Lengkapi deskripsi'])
        ->assertSessionHas('success', 'Business status updated to Need revision');

    $business->refresh();
    expect($business->approval_status)->toBe('need_revision')
        ->and($business->rejection_reason)->toBe('Lengkapi deskripsi')
        ->and($business->is_visible)->toBeFalse();

    $this->actingAs($w['admin'])->post(route('businesses.update-status', $business), ['status' => 'approved', 'rejection_reason' => 'ignored']);
    $business->refresh();
    expect($business->rejection_reason)->toBeNull()->and($business->is_visible)->toBeTrue();

    $this->actingAs($w['owner'])->post(route('businesses.update-status', $business), ['status' => 'approved'])->assertForbidden();
});

test('admin approve makes a business visible', function () {
    $w = businessWorld();
    $business = approvedBusiness($w['owner'], $w['category'], ['is_visible' => false]);

    $this->actingAs($w['admin'])->post(route('businesses.approve', $business))
        ->assertSessionHas('success', 'Business "Approved Venture" has been approved and is now visible.');
    expect($business->fresh()->is_visible)->toBeTrue();
});

test('featured toggle answers json and refuses unapproved businesses', function () {
    $w = businessWorld();
    $business = approvedBusiness($w['owner'], $w['category']);
    $pending = approvedBusiness($w['owner'], $w['category'], ['name' => 'Pending One', 'approval_status' => 'pending']);

    $this->actingAs($w['admin'])->postJson(route('businesses.toggle-featured', $business))
        ->assertExactJson(['success' => true, 'message' => '"Approved Venture" is now featured.']);
    $this->actingAs($w['admin'])->postJson(route('businesses.toggle-featured', $business))
        ->assertExactJson(['success' => true, 'message' => '"Approved Venture" removed from featured.']);
    $this->actingAs($w['admin'])->postJson(route('businesses.toggle-featured', $pending))
        ->assertStatus(422)->assertJson(['success' => false]);
    $this->actingAs($w['admin'])->post(route('businesses.toggle-featured', $pending))
        ->assertSessionHasErrors('approval');
});

test('admin delete removes the business and returns to the directory', function () {
    $w = businessWorld();
    $business = approvedBusiness($w['owner'], $w['category']);

    $this->actingAs($w['admin'])->delete(route('businesses.destroy', $business))
        ->assertRedirect(route('businesses.index'))
        ->assertSessionHas('success', 'Business deleted successfully.');
    expect(Business::find($business->id))->toBeNull();
});

test('admin business list counts and filters entrepreneurs', function () {
    $w = businessWorld();
    approvedBusiness($w['owner'], $w['category'], ['name' => 'Featured One', 'is_featured' => true]);
    approvedBusiness($w['owner'], $w['category'], ['name' => 'Waiting One', 'approval_status' => 'pending']);
    approvedBusiness($w['owner'], $w['category'], ['name' => 'Rejected One', 'approval_status' => 'rejected']);

    $response = $this->actingAs($w['admin'])->get('/admin/business')->assertOk()->assertViewIs('businesses.admin.index');
    expect([
        $response->viewData('totalBusinesses'), $response->viewData('approvedBusinesses'),
        $response->viewData('pendingBusinesses'), $response->viewData('rejectedBusinesses'),
        $response->viewData('featuredBusinessesCount'),
    ])->toBe([3, 1, 1, 1, 1]);

    $filtered = $this->actingAs($w['admin'])->get('/admin/business?status=pending');
    expect($filtered->viewData('businesses')->pluck('name')->all())->toBe(['Waiting One']);

    $this->actingAs($w['admin'])->get('/admin/business', ['X-Requested-With' => 'XMLHttpRequest'])
        ->assertViewIs('businesses.admin.partials.list');
});

test('admin company list counts work profiles', function () {
    $w = businessWorld();
    Company::create(['user_id' => $w['owner']->id, 'name' => 'A', 'approval_status' => 'approved']);
    Company::create(['user_id' => $w['owner']->id, 'name' => 'B', 'approval_status' => 'pending']);

    $response = $this->actingAs($w['admin'])->get('/admin/business?type=intrapreneur')->assertOk();
    expect([$response->viewData('totalBusinesses'), $response->viewData('approvedBusinesses'), $response->viewData('pendingBusinesses')])
        ->toBe([2, 1, 1])
        ->and($response->viewData('viewType'))->toBe('intrapreneur');
});

test('business import needs a file and queues the importer', function () {
    $w = businessWorld();
    Excel::fake();

    $this->actingAs($w['admin'])->post(route('businesses.import'), [])->assertSessionHasErrors('file');

    $file = UploadedFile::fake()->createWithContent('entrepreneur.csv', "Timestamp,Full Name\n2026-01-01,Test\n");
    $this->actingAs($w['admin'])->postJson(route('businesses.import'), ['file' => $file])
        ->assertOk()->assertJson(['success' => true, 'format' => 'Form Response']);
});

// --- Work profiles ---

test('a student work profile is stored pending, an admin one approved, with title-cased location', function () {
    $w = businessWorld();
    $payload = ['category_id' => $w['category']->id, 'position' => 'Staff', 'job_description' => 'Mengelola data penjualan.', 'province' => '35', 'city' => '3578'];

    $this->actingAs($w['owner'])->post(route('intrapreneurs.store'), $payload + ['name' => 'PT Student'])
        ->assertSessionHas('success', 'Work profile registered successfully!');
    $this->actingAs($w['admin'])->post(route('intrapreneurs.store'), $payload + ['name' => 'PT Admin']);

    $student = Company::where('name', 'PT Student')->firstOrFail();
    expect($student->approval_status)->toBe('pending')
        ->and($student->is_visible)->toBeTrue()
        ->and($student->province)->toBe('Jawa Timur')
        ->and($student->city)->toBe('Kota Surabaya');
    expect(Company::where('name', 'PT Admin')->value('approval_status'))->toBe('approved');
    $this->actingAs($w['owner'])->post(route('intrapreneurs.store'), ['name' => 'X'])->assertSessionHasErrors(['category_id', 'position', 'job_description']);
});

test('renaming a work profile regenerates its slug; strangers are refused', function () {
    $w = businessWorld();
    $company = Company::create(['user_id' => $w['owner']->id, 'name' => 'Old Employer', 'category_id' => $w['category']->id]);
    Company::create(['user_id' => $w['stranger']->id, 'name' => 'New Employer']);
    $payload = ['name' => 'New Employer', 'category_id' => $w['category']->id, 'position' => 'Lead', 'job_description' => 'Memimpin tim penjualan.'];

    $this->actingAs($w['stranger'])->get(route('intrapreneurs.edit', $company))->assertForbidden();
    $this->actingAs($w['stranger'])->put(route('intrapreneurs.update', $company), $payload)->assertForbidden();

    $this->actingAs($w['owner'])->get(route('intrapreneurs.edit', $company))->assertOk();
    $this->actingAs($w['owner'])->put(route('intrapreneurs.update', $company), $payload)
        ->assertSessionHas('success', 'Work profile updated successfully!');

    expect($company->fresh()->slug)->toBe('new-employer-1');
});

// --- Business page data ---

test('business page data: offering mode, instagram handles and whatsapp links', function () {
    ['category' => $category, 'owner' => $owner, 'stranger' => $stranger] = businessWorld();
    $owner->update(['whatsapp' => '+62 812-0000-1111']);
    $business = approvedBusiness($owner, $category, ['instagram' => '@first, second;@ ; 0', 'offering_type' => null]);
    $product = \App\Models\Product::create(['business_id' => $business->id, 'type' => 'product', 'name' => 'Kopi Susu']);
    $service = \App\Models\Product::create(['business_id' => $business->id, 'type' => 'service', 'name' => 'Catering']);

    expect($business->offering_mode)->toBe('product')
        ->and($business->instagram_handles)->toBe(['first', 'second'])
        ->and($business->whatsappLinkFor($product))->toBe('https://wa.me/6281200001111?text='.urlencode('Hi, I am interested in your product: "Kopi Susu" listed on UC Online Learning.'));

    $business->update(['whatsapp' => '0899', 'offering_type' => 'both']);
    expect($business->fresh()->offering_mode)->toBe('both')
        ->and($business->fresh()->whatsappLinkFor($service))->toStartWith('https://wa.me/0899?text=Hi%2C+I+am+interested+in+your+service');

    $collab = Collab::create(['sender_id' => $owner->id, 'recipient_id' => $stranger->id, 'status' => 'accepted']);
    expect($collab->partnerFor($owner)->id)->toBe($stranger->id)
        ->and($collab->partnerFor($stranger)->id)->toBe($owner->id);
});
