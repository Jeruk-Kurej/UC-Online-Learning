<?php

use App\Models\Business;
use App\Models\Category;
use App\Models\Company;
use App\Models\User;
use Illuminate\Support\Facades\DB;

// --- Every query costs 20 to 30 ms on the live database: the busiest pages keep to a fixed number ---

function budgetVenture(string $name, string $province, string $city, bool $featured = true): Business
{
    $owner = User::factory()->create(['is_visible' => true]);
    $business = new Business(['user_id' => $owner->id, 'name' => $name, 'description' => 'Sells things.', 'category_id' => Category::firstOrCreate(['slug' => 'jasa'], ['name' => 'Jasa'])->id, 'province' => $province, 'city' => $city, 'is_visible' => true, 'type' => 'entrepreneur']);
    $business->approval_status = 'approved';
    $business->is_featured = $featured;
    $business->save();

    return $business;
}

function queriesOf(string $path): int
{
    DB::flushQueryLog();
    DB::enableQueryLog();
    test()->get($path)->assertOk();

    return count(DB::getQueryLog());
}

test('the home page runs nine queries however many sections have items', function () {
    foreach (range(1, 4) as $i) {
        budgetVenture("Venture {$i}", 'Jawa Timur', 'Kota Surabaya');
    }
    $entrepreneur = User::factory()->create(['is_featured' => true, 'current_status' => 'Entrepreneur', 'profile_photo_url' => 'https://example.test/e.jpg']);
    $intrapreneur = User::factory()->create(['is_featured' => true, 'current_status' => 'Intrapreneur', 'profile_photo_url' => 'https://example.test/i.jpg']);
    Company::create(['user_id' => $intrapreneur->id, 'name' => 'Budget Corp', 'category_id' => Category::first()->id, 'is_visible' => true, 'approval_status' => 'approved']);

    expect(queriesOf('/'))->toBe(9);
    $this->get('/')->assertSee($entrepreneur->name)->assertSee($intrapreneur->name)->assertSee('Budget Corp');
});

test('the directory runs seven queries, five for work profiles', function () {
    foreach (range(1, 8) as $i) {
        $venture = budgetVenture("Venture {$i}", 'Jawa Timur', 'Kota Surabaya', false);
        Company::create(['user_id' => $venture->user_id, 'name' => "Employer {$i}", 'category_id' => $venture->category_id, 'province' => 'Jawa Timur', 'city' => 'Kota Surabaya', 'is_visible' => true, 'approval_status' => 'approved']);
    }

    expect(queriesOf('/business'))->toBe(7)
        ->and(queriesOf('/business?view=intrapreneur'))->toBe(5);
});

test('the directory filters list a province or city once, whatever its case or trailing spaces', function () {
    budgetVenture('First', 'Jawa Timur', 'Kota Surabaya', false);
    budgetVenture('Second', 'JAWA TIMUR ', 'KOTA SURABAYA', false);
    budgetVenture('Third', 'Bali', 'Denpasar', false);

    $response = $this->get('/business');
    expect($response->viewData('availableProvinces')->values()->all())->toBe(['Bali', 'Jawa Timur'])
        ->and($response->viewData('directoryState')['availableCities']->all())->toBe(['Denpasar', 'Kota Surabaya']);

    $selected = $this->get('/business?province=jawa%20timur')->viewData('directoryState');
    expect($selected['availableCities']->all())->toBe(['Kota Surabaya']);
});
