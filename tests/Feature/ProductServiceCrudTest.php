<?php

use App\Models\Business;
use App\Models\Product;
use App\Models\User;

test('business owner can create, edit and delete products', function () {
    $owner = User::factory()->create();
    $business = Business::create([
        'user_id' => $owner->id,
        'name' => 'Owner Cafe',
        'slug' => 'owner-cafe',
        'offering_type' => 'both',
        'is_visible' => true,
        'type' => 'entrepreneur',
    ]);

    // 1. Create product
    $response = $this->actingAs($owner)
        ->post(route('businesses.products.store', $business), [
            'name' => 'Choco Milkshake',
            'description' => 'A sweet milkshake',
            'price' => 25000,
        ]);

    $response->assertSessionHasNoErrors();
    $response->assertRedirect(route('businesses.show', $business));

    $this->assertDatabaseHas('products', [
        'business_id' => $business->id,
        'name' => 'Choco Milkshake',
        'type' => 'product',
        'price' => 25000,
    ]);

    $product = Product::where('name', 'Choco Milkshake')->first();

    // 2. Edit product
    $response = $this->actingAs($owner)
        ->put(route('businesses.products.update', [$business, $product]), [
            'name' => 'Sweet Choco Milkshake',
            'description' => 'A very sweet milkshake',
            'price' => 27000,
        ]);

    $response->assertSessionHasNoErrors();
    $response->assertRedirect(route('businesses.products.show', [$business, $product]));

    $this->assertDatabaseHas('products', [
        'id' => $product->id,
        'name' => 'Sweet Choco Milkshake',
        'price' => 27000,
    ]);

    // 3. Delete product
    $response = $this->actingAs($owner)
        ->delete(route('businesses.products.destroy', [$business, $product]));

    $response->assertSessionHasNoErrors();
    $response->assertRedirect(route('businesses.show', $business));

    $this->assertDatabaseMissing('products', [
        'id' => $product->id,
    ]);
});

test('business owner can create, edit and delete services', function () {
    $owner = User::factory()->create();
    $business = Business::create([
        'user_id' => $owner->id,
        'name' => 'Owner Agency',
        'slug' => 'owner-agency',
        'offering_type' => 'both',
        'is_visible' => true,
        'type' => 'entrepreneur',
    ]);

    // 1. Create service
    $response = $this->actingAs($owner)
        ->post(route('businesses.services.store', $business), [
            'name' => 'Logo Design',
            'description' => 'Professional logo design',
            'price' => 500000,
            'price_type' => 'starting_from',
        ]);

    $response->assertSessionHasNoErrors();
    $response->assertRedirect(route('businesses.show', $business));

    $this->assertDatabaseHas('products', [
        'business_id' => $business->id,
        'name' => 'Logo Design',
        'type' => 'service',
        'price' => 500000,
        'price_type' => 'starting_from',
    ]);

    $service = Product::where('name', 'Logo Design')->first();

    // 2. Edit service
    $response = $this->actingAs($owner)
        ->put(route('businesses.services.update', [$business, $service]), [
            'name' => 'Elite Logo Design',
            'description' => 'Professional logo design with extra files',
            'price' => 700000,
            'price_type' => 'fixed',
        ]);

    $response->assertSessionHasNoErrors();
    $response->assertRedirect(route('businesses.show', $business));

    $this->assertDatabaseHas('products', [
        'id' => $service->id,
        'name' => 'Elite Logo Design',
        'price' => 700000,
        'price_type' => 'fixed',
    ]);

    // 3. Delete service
    $response = $this->actingAs($owner)
        ->delete(route('businesses.services.destroy', [$business, $service]));

    $response->assertSessionHasNoErrors();
    $response->assertRedirect(route('businesses.show', $business));

    $this->assertDatabaseMissing('products', [
        'id' => $service->id,
    ]);
});

test('non-owners cannot manage products or services', function () {
    $owner = User::factory()->create();
    $nonOwner = User::factory()->create();
    $business = Business::create([
        'user_id' => $owner->id,
        'name' => 'Cafe',
        'slug' => 'cafe',
        'offering_type' => 'both',
        'is_visible' => true,
        'type' => 'entrepreneur',
    ]);

    $product = Product::create([
        'business_id' => $business->id,
        'name' => 'Tea',
        'description' => 'Hot tea',
        'price' => 5000,
        'type' => 'product',
    ]);

    $service = Product::create([
        'business_id' => $business->id,
        'name' => 'Clean Room',
        'description' => 'Cleaning',
        'price' => 50000,
        'price_type' => 'fixed',
        'type' => 'service',
    ]);

    // Try to create product as non-owner
    $response = $this->actingAs($nonOwner)
        ->post(route('businesses.products.store', $business), [
            'name' => 'Coffee',
            'description' => 'Hot coffee',
            'price' => 10000,
        ]);
    $response->assertStatus(403);

    // Try to edit product as non-owner
    $response = $this->actingAs($nonOwner)
        ->put(route('businesses.products.update', [$business, $product]), [
            'name' => 'Elixir Tea',
            'description' => 'Magical tea',
            'price' => 15000,
        ]);
    $response->assertStatus(403);

    // Try to delete product as non-owner
    $response = $this->actingAs($nonOwner)
        ->delete(route('businesses.products.destroy', [$business, $product]));
    $response->assertStatus(403);

    // Try to create service as non-owner
    $response = $this->actingAs($nonOwner)
        ->post(route('businesses.services.store', $business), [
            'name' => 'Laundry',
            'description' => 'Clean clothes',
            'price' => 30000,
            'price_type' => 'fixed',
        ]);
    $response->assertStatus(403);

    // Try to edit service as non-owner
    $response = $this->actingAs($nonOwner)
        ->put(route('businesses.services.update', [$business, $service]), [
            'name' => 'Elite Clean Room',
            'description' => 'Extra cleaning',
            'price' => 60000,
            'price_type' => 'fixed',
        ]);
    $response->assertStatus(403);

    // Try to delete service as non-owner
    $response = $this->actingAs($nonOwner)
        ->delete(route('businesses.services.destroy', [$business, $service]));
    $response->assertStatus(403);
});
