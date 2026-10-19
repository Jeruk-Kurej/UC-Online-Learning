<?php

use App\Models\Business;
use App\Models\Product;
use App\Models\User;

// --- Home page: every featured item is equal and the order changes daily ---

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

test('every featured venture is listed, none is singled out in the hero', function () {
    featuredVenture('Alpha Venture');
    featuredVenture('Beta Venture');

    $html = $this->get('/featured')->assertOk()->assertSee('Alpha Venture')->assertSee('Beta Venture')->getContent();

    expect(substr_count($html, 'Visit venture'))->toBe(2)->and($html)->not->toContain('Featured venture<');
});

test('the order stays the same within a day and changes with the day', function () {
    foreach (range(1, 8) as $i) {
        featuredVenture("Venture number {$i}");
    }
    $names = fn () => collect($this->get('/featured')->viewData('featuredVentures'))->pluck('name')->all();

    $first = $names();
    expect($names())->toBe($first);

    $this->travel(1)->days();
    expect($names())->not->toBe($first);
});

test('a section shows at most six items', function () {
    foreach (range(1, 9) as $i) {
        featuredVenture("Venture number {$i}");
    }

    expect($this->get('/featured')->viewData('featuredVentures'))->toHaveCount(6);
});

test('without featured ventures the page says so', function () {
    $this->get('/featured')->assertOk()->assertSee('No featured ventures yet');
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
