<?php

use App\Models\Business;
use App\Models\Category;
use App\Models\Company;
use App\Models\Product;
use App\Models\User;

// --- Editing a venture: offering mode, a second review after "needs revision", placeholders without third parties ---

function flowCategory(): Category
{
    return Category::firstOrCreate(['slug' => 'jasa'], ['name' => 'Jasa']);
}

function flowBusiness(User $owner, string $mode = 'both', string $status = 'approved'): Business
{
    $business = new Business(['user_id' => $owner->id, 'name' => 'Flow Venture', 'description' => 'Sells things.', 'category_id' => flowCategory()->id, 'offering_type' => $mode, 'is_visible' => $status === 'approved', 'type' => 'entrepreneur']);
    $business->approval_status = $status;
    $business->rejection_reason = $status === 'need_revision' ? 'Add a clearer description.' : null;
    $business->save();

    return $business;
}

function flowPayload(Business $business, array $overrides = []): array
{
    return $overrides + ['name' => $business->name, 'description' => 'Sells better things.', 'category_id' => $business->category_id, 'business_mode' => $business->offering_type];
}

test('a business cannot drop a kind of offering it still has, in either direction', function () {
    $owner = User::factory()->create();
    $business = flowBusiness($owner);
    Product::create(['business_id' => $business->id, 'type' => 'service', 'name' => 'Consulting', 'description' => 'Help', 'price_type' => 'negotiable']);

    $this->actingAs($owner)->put(route('businesses.update', $business), flowPayload($business, ['business_mode' => 'product']))
        ->assertSessionHasErrors(['business_mode' => 'This business still has services. Delete them first, or choose "Products and services".']);
    expect($business->fresh()->offering_type)->toBe('both');

    $this->actingAs($owner)->put(route('businesses.update', $business), flowPayload($business, ['business_mode' => 'service']))->assertSessionHasNoErrors();
    expect($business->fresh()->offering_type)->toBe('service');

    $products = flowBusiness($owner);
    Product::create(['business_id' => $products->id, 'type' => 'product', 'name' => 'Mug', 'description' => 'A mug', 'price_type' => 'fixed', 'price' => 1000]);
    $this->actingAs($owner)->put(route('businesses.update', $products), flowPayload($products, ['business_mode' => 'service']))
        ->assertSessionHasErrors(['business_mode' => 'This business still has products. Delete them first, or choose "Products and services".']);
});

test('a student saving a business that needs revision sends it back for review; other statuses and admin edits stay', function () {
    $owner = User::factory()->create();
    $admin = User::factory()->admin()->create();

    $revision = flowBusiness($owner, status: 'need_revision');
    $this->actingAs($owner)->put(route('businesses.update', $revision), flowPayload($revision))
        ->assertSessionHas('success', 'Business updated and sent to the admin team for another review.');
    expect($revision->fresh()->only(['approval_status', 'rejection_reason']))->toBe(['approval_status' => 'pending', 'rejection_reason' => null]);

    $adminEdit = flowBusiness($owner, status: 'need_revision');
    $this->actingAs($admin)->put(route('businesses.update', $adminEdit), flowPayload($adminEdit, ['user_id' => $owner->id]))
        ->assertSessionHas('success', 'Business updated successfully!');
    expect($adminEdit->fresh()->approval_status)->toBe('need_revision');

    foreach (['approved', 'rejected'] as $status) {
        $kept = flowBusiness($owner, status: $status);
        $this->actingAs($owner)->put(route('businesses.update', $kept), flowPayload($kept))->assertSessionHas('success', 'Business updated successfully!');
        expect($kept->fresh()->approval_status)->toBe($status);
    }
});

test('a student saving a work profile that needs revision sends it back for review', function () {
    $owner = User::factory()->create();
    $company = Company::create(['user_id' => $owner->id, 'name' => 'Flow Corp', 'category_id' => flowCategory()->id, 'position' => 'Lead', 'job_description' => 'Leads the sales team.', 'approval_status' => 'need_revision', 'rejection_reason' => 'Say what you do.']);

    $this->actingAs($owner)->put(route('intrapreneurs.update', $company), ['name' => 'Flow Corp', 'category_id' => $company->category_id, 'position' => 'Lead', 'job_description' => 'Leads the sales team of six.'])
        ->assertSessionHas('success', 'Work profile updated and sent to the admin team for another review.');

    expect($company->fresh()->only(['approval_status', 'rejection_reason']))->toBe(['approval_status' => 'pending', 'rejection_reason' => null]);
});

test('the owner checks on the business form answer in English', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)->post(route('businesses.store'), ['name' => 'X', 'description' => 'x', 'category_id' => flowCategory()->id, 'business_mode' => 'both', 'user_id' => $admin->id])
        ->assertSessionHasErrors(['user_id' => 'An admin account cannot own or run a business. Choose a student.']);
});

test('a missing photo or logo is drawn here, never fetched from an avatar service', function () {
    $user = User::factory()->create(['name' => 'budi <santoso>']);
    $legacy = User::factory()->create(['profile_photo_url' => 'https://ui-avatars.com/api/?name=Old']);
    $real = User::factory()->create(['profile_photo_url' => 'https://res.cloudinary.com/demo/image/upload/p.jpg']);

    expect($user->profile_photo_url)->toStartWith('data:image/svg+xml,')
        ->and(rawurldecode($user->profile_photo_url))->toContain('>B</text>')
        ->and($user->has_profile_photo)->toBeFalse()
        ->and($legacy->profile_photo_url)->toStartWith('data:image/svg+xml,')
        ->and($legacy->has_profile_photo)->toBeFalse()
        ->and($real->profile_photo_url)->toBe('https://res.cloudinary.com/demo/image/upload/p.jpg')
        ->and($real->has_profile_photo)->toBeTrue()
        ->and(flowBusiness($user)->logo_url)->toStartWith('data:image/svg+xml,');

    $this->get(route('businesses.show', flowBusiness($real)))->assertOk()->assertDontSee('ui-avatars.com');
});
