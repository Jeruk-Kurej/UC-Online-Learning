<?php

use App\Models\Category;
use App\Models\Company;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

// --- Who owns what, who sees what, and which form changes what ---

test('the profile form changes neither the password nor the testimony', function () {
    $user = User::factory()->create(['testimony' => 'Kept testimony']);

    $this->actingAs($user)->patch(route('profile.update'), [
        'name' => $user->name,
        'password' => 'new-secret-123',
        'password_confirmation' => 'new-secret-123',
        'testimony' => str_repeat('a', 400),
    ])->assertRedirect(route('profile.edit'));

    $user->refresh();
    expect(Hash::check('password', $user->password))->toBeTrue()
        ->and($user->getRawOriginal('testimony'))->toBe('Kept testimony');
});

test('collab requests need two visible students', function () {
    $sender = User::factory()->create();
    $hidden = User::factory()->create(['is_visible' => false]);

    $this->actingAs($sender)->post(route('collabs.store', $hidden))->assertForbidden();
    $this->actingAs($hidden)->post(route('collabs.store', $sender))->assertForbidden();
});

test('a hidden profile and an admin profile are not public; the person and admins still open them', function () {
    $hidden = User::factory()->create(['is_visible' => false]);
    $admin = User::factory()->admin()->create();
    $stranger = User::factory()->create();

    $this->get('/'.$hidden->slug)->assertNotFound();
    $this->actingAs($stranger)->get('/'.$hidden->slug)->assertNotFound();
    $this->actingAs($hidden)->get('/'.$hidden->slug)->assertOk();
    $this->actingAs($admin)->get('/'.$hidden->slug)->assertOk();

    auth()->logout();
    $this->get('/'.$admin->slug)->assertNotFound();
});

test('a profile lists only approved work profiles', function () {
    $student = User::factory()->create();
    $category = Category::create(['name' => 'Jasa', 'slug' => 'jasa']);
    Company::create(['user_id' => $student->id, 'name' => 'Approved Employer', 'category_id' => $category->id, 'is_visible' => true, 'approval_status' => 'approved']);
    Company::create(['user_id' => $student->id, 'name' => 'Waiting Employer', 'category_id' => $category->id, 'is_visible' => true, 'approval_status' => 'pending']);

    $companies = $this->get('/'.$student->slug)->assertOk()->viewData('user')->companies;

    expect($companies->pluck('name')->all())->toBe(['Approved Employer']);
});

test('admins pick the student owner on both venture forms and cancel back to the admin list', function () {
    $admin = User::factory()->admin()->create();
    $student = User::factory()->create(['name' => 'Picked Student']);

    $businessForm = $this->actingAs($admin)->get(route('businesses.create'))->assertOk()->getContent();
    expect($businessForm)->toContain('name="user_id"', 'Picked Student', 'href="'.route('businesses.admin').'" class="btn btn-secondary">Cancel')
        ->and($businessForm)->not->toContain('('.$admin->email.')');

    $workForm = $this->actingAs($admin)->get(route('intrapreneurs.create'))->assertOk()->getContent();
    expect($workForm)->toContain('name="user_id"', 'Picked Student', 'href="'.route('businesses.admin').'" class="btn btn-secondary">Cancel');

    $studentForm = $this->actingAs($student)->get(route('intrapreneurs.create'))->assertOk()->getContent();
    expect($studentForm)->not->toContain('name="user_id"')->and($studentForm)->toContain('href="'.route('businesses.my').'" class="btn btn-secondary">Cancel');
});
