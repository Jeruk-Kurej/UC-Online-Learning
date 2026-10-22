<?php

use App\Models\Business;
use App\Models\Category;
use App\Models\Company;
use App\Models\User;
use Illuminate\Support\Facades\Route;

// --- A new name never takes an address the site already answers, and businesses and work profiles never share one ---

function clashBusiness(User $owner, string $name): Business
{
    $business = new Business(['user_id' => $owner->id, 'name' => $name, 'description' => 'Sells things.', 'category_id' => Category::firstOrCreate(['slug' => 'jasa'], ['name' => 'Jasa'])->id, 'offering_type' => 'both', 'is_visible' => true, 'type' => 'entrepreneur']);
    $business->approval_status = 'approved';
    $business->save();

    return $business;
}

function clashCompany(User $owner, string $name): Company
{
    return Company::create(['user_id' => $owner->id, 'name' => $name, 'position' => 'Lead', 'job_description' => 'Leads the team.', 'approval_status' => 'approved', 'is_visible' => true]);
}

test('a person named like a page of the site gets an address of their own, and the page stays where it was', function () {
    $about = User::factory()->create(['name' => 'About']);
    $inbox = User::factory()->create(['name' => 'Inbox']);

    expect($about->slug)->toBe('about-1')->and($inbox->slug)->toBe('inbox-1');
    $this->get(route('users.show', $about))->assertOk()->assertViewIs('users.show')->assertViewHas('user', fn (User $shown) => $shown->is($about));
    $this->get('/about')->assertOk()->assertViewMissing('user');
});

test('no fixed one-word address of the site, and no folder in public/, can become a profile address', function () {
    $words = collect(Route::getRoutes()->getRoutes())
        ->filter(fn ($route) => in_array('GET', $route->methods(), true) && preg_match('/^[a-z][a-z-]*$/', $route->uri()) === 1)
        ->map(fn ($route) => $route->uri())
        ->merge(['images', 'build'])
        ->unique();

    expect($words)->toContain('about', 'login', 'inbox', 'profile', 'featured', 'business', 'my-business');

    foreach ($words as $word) {
        expect(User::factory()->create(['name' => $word])->slug)->not->toBe($word);
    }
});

test('a business named Create opens at its own address, and /business/create still opens the form', function () {
    $owner = User::factory()->create();
    $business = clashBusiness($owner, 'Create');

    expect($business->slug)->toBe('create-1');
    $this->get(route('businesses.show', $business))->assertOk()->assertViewIs('businesses.show');
    $this->actingAs($owner)->get('/business/create')->assertOk()->assertViewIs('businesses.form');
});

test('a business and a work profile with the same name get different addresses and each opens its own page', function () {
    $owner = User::factory()->create();
    $business = clashBusiness($owner, 'Kopi Senja');
    $company = clashCompany($owner, 'Kopi Senja');

    expect($business->slug)->toBe('kopi-senja')->and($company->slug)->toBe('kopi-senja-1');
    $this->get('/business/kopi-senja')->assertOk()->assertViewIs('businesses.show');
    $this->get('/business/kopi-senja-1')->assertOk()->assertViewIs('work-profiles.show');

    $firstCompany = clashCompany($owner, 'Teh Pagi');
    expect(clashBusiness($owner, 'Teh Pagi')->slug)->toBe('teh-pagi-1')
        ->and($firstCompany->freshSlugFor('Kopi Senja'))->toBe('kopi-senja-2')
        ->and($company->freshSlugFor('Kopi Senja'))->toBe('kopi-senja-1');
});

test('ordinary names keep the addresses they always had', function () {
    expect(User::factory()->create(['name' => 'Budi Santoso'])->slug)->toBe('budi-santoso')
        ->and(User::factory()->create(['name' => 'Budi Santoso'])->slug)->toBe('budi-santoso-1')
        ->and(clashBusiness(User::factory()->create(), 'Warung Budi')->slug)->toBe('warung-budi');
});
