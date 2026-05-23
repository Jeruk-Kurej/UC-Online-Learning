<?php

use App\Models\Business;
use App\Models\User;

test('admin can feature more than 4 users', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    // Create 4 already featured users
    User::factory()->count(4)->create([
        'is_featured' => true,
        'is_visible' => true,
        'role' => 'user',
    ]);

    // Create a 5th user to toggle
    $targetUser = User::factory()->create([
        'is_featured' => false,
        'is_visible' => true,
        'role' => 'user',
    ]);

    $response = $this->actingAs($admin)
        ->post(route('users.toggle-featured', $targetUser));

    $response->assertSessionHasNoErrors();
    $this->assertTrue($targetUser->fresh()->is_featured);
    $this->assertEquals(5, User::where('is_featured', true)->count());
});

test('admin can feature more than 8 businesses', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    // Create 8 already featured businesses
    for ($i = 0; $i < 8; $i++) {
        $owner = User::factory()->create();
        Business::create([
            'user_id' => $owner->id,
            'name' => "Business {$i}",
            'slug' => "business-{$i}",
            'offering_type' => 'product',
            'is_visible' => true,
            'is_featured' => true,
            'type' => 'entrepreneur',
        ]);
    }

    // Create a 9th business to toggle
    $targetOwner = User::factory()->create();
    $targetBusiness = Business::create([
        'user_id' => $targetOwner->id,
        'name' => "Target Business",
        'slug' => "target-business",
        'offering_type' => 'product',
        'is_visible' => true,
        'is_featured' => false,
        'type' => 'entrepreneur',
    ]);

    $response = $this->actingAs($admin)
        ->post(route('businesses.toggle-featured', $targetBusiness));

    $response->assertSessionHasNoErrors();
    $this->assertTrue($targetBusiness->fresh()->is_featured);
    $this->assertEquals(9, Business::where('is_featured', true)->count());
});

test('non-admin cannot toggle featured status of users or businesses', function () {
    $nonAdmin = User::factory()->create(['role' => 'user']);
    $targetUser = User::factory()->create(['is_featured' => false, 'is_visible' => true]);
    
    $owner = User::factory()->create();
    $targetBusiness = Business::create([
        'user_id' => $owner->id,
        'name' => "Some Business",
        'slug' => "some-business",
        'offering_type' => 'product',
        'is_visible' => true,
        'is_featured' => false,
        'type' => 'entrepreneur',
    ]);

    // Try user
    $response = $this->actingAs($nonAdmin)
        ->post(route('users.toggle-featured', $targetUser));
    $response->assertStatus(403);
    $this->assertFalse($targetUser->fresh()->is_featured);

    // Try business
    $response = $this->actingAs($nonAdmin)
        ->post(route('businesses.toggle-featured', $targetBusiness));
    $response->assertStatus(403);
    $this->assertFalse($targetBusiness->fresh()->is_featured);
});
