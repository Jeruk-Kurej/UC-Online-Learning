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

    $html = $this->get('/')->assertOk()->assertSee('Alpha Venture')->assertSee('Beta Venture')->getContent();

    expect(substr_count($html, 'by '))->toBeGreaterThanOrEqual(2)->and($html)->not->toContain('Featured venture<');
});

test('the order stays the same within a day and changes with the day', function () {
    foreach (range(1, 8) as $i) {
        featuredVenture("Venture number {$i}");
    }
    $names = fn () => collect($this->get('/')->viewData('featuredVentures'))->pluck('name')->all();

    $first = $names();
    expect($names())->toBe($first);

    $this->travel(1)->days();
    expect($names())->not->toBe($first);
});

test('a section shows at most six items', function () {
    foreach (range(1, 9) as $i) {
        featuredVenture("Venture number {$i}");
    }

    expect($this->get('/')->viewData('featuredVentures'))->toHaveCount(6);
});

test('without anything featured the page says so, and a group without items is left out', function () {
    $this->get('/')->assertOk()->assertSee('Nothing to show yet')->assertDontSee('See all ventures');

    featuredVenture('Only Venture');
    $this->get('/')->assertOk()->assertDontSee('Nothing to show yet')->assertSee('See all ventures')->assertDontSee('Our <span class="text-brand-700">entrepreneurs</span>', false);
});

test('ventures, entrepreneurs and intrapreneurs are all on the page without tabs', function () {
    featuredVenture('Some Venture');
    User::factory()->create(['is_visible' => true, 'is_featured' => true, 'current_status' => 'Entrepreneur', 'profile_photo_url' => 'https://example.test/e.jpg', 'name' => 'Featured Founder']);
    User::factory()->create(['is_visible' => true, 'is_featured' => true, 'current_status' => 'Intrapreneur', 'profile_photo_url' => 'https://example.test/i.jpg', 'name' => 'Featured Employee']);

    $this->get('/')->assertOk()->assertSee('Some Venture')->assertSee('Featured Founder')->assertSee('Featured Employee')->assertDontSee('role="tab"', false);
});

test('a featured venture shows the photo of its first product that has one', function () {
    $business = featuredVenture('Photo Venture', 'https://example.test/photo.jpg');

    expect($business->fresh()->load('products')->showcase_photo_url)->toBe('https://example.test/photo.jpg');
    $this->get('/')->assertOk()->assertSee('https://example.test/photo.jpg', false);
});

test('guests are asked to log in at the bottom, signed-in people are not', function () {
    $this->get('/')->assertSee('Do you study or did you graduate');
    $this->actingAs(User::factory()->create())->get('/')->assertDontSee('Do you study or did you graduate');
});

test('the old /featured address leads to the home page', function () {
    $this->get('/featured')->assertRedirect('/')->assertStatus(301);
});
