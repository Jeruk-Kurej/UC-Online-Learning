<?php

use App\Models\Business;
use App\Models\Product;
use App\Models\User;

// --- Home page: the first featured venture sits in the hero, the others below ---

function featuredVenture(string $name, ?string $photo = null): Business
{
    $owner = User::factory()->create(['is_visible' => true]);
    $business = new Business(['user_id' => $owner->id, 'name' => $name, 'is_visible' => true, 'type' => 'entrepreneur', 'description' => 'Sells things.']);
    $business->approval_status = 'approved';
    $business->is_featured = true;
    $business->save();

    if ($photo) {
        Product::create(['business_id' => $business->id, 'type' => 'product', 'name' => 'Thing', 'description' => 'A thing', 'photo_url' => $photo]);
    }

    return $business;
}

test('the hero shows the newest featured venture and the section lists the rest', function () {
    featuredVenture('Older Venture');
    $newest = featuredVenture('Newest Venture');
    $newest->forceFill(['created_at' => now()->addMinute()])->save();

    $html = $this->get('/featured')->assertOk()->getContent();

    expect(strpos($html, 'Newest Venture'))->toBeLessThan(strpos($html, 'More featured ventures'))
        ->and(strpos($html, 'More featured ventures'))->toBeLessThan(strpos($html, 'Older Venture'));
});

test('with a single featured venture the section below the hero is left out', function () {
    featuredVenture('Only Venture');

    $this->get('/featured')->assertOk()->assertSee('Only Venture')->assertDontSee('More featured ventures');
});

test('without featured ventures the page says so', function () {
    $this->get('/featured')->assertOk()->assertSee('No featured ventures yet')->assertDontSee('Featured venture<', false);
});

test('a featured venture shows the photo of its first product that has one', function () {
    $business = featuredVenture('Photo Venture', 'https://example.test/photo.jpg');

    expect($business->fresh()->load('products')->showcase_photo_url)->toBe('https://example.test/photo.jpg');
    $this->get('/featured')->assertOk()->assertSee('https://example.test/photo.jpg', false);
});

test('guests are asked to log in at the bottom, signed-in people are not', function () {
    $this->get('/featured')->assertSee('Do you study or did you graduate');
    $this->actingAs(User::factory()->create())->get('/featured')->assertDontSee('Do you study or did you graduate');
});
