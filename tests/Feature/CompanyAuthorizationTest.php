<?php

use App\Models\Business;
use App\Models\Category;
use App\Models\Company;
use App\Models\User;

// --- Who may manage a work profile: its owner and admins, nobody else ---

function companyActors(): array
{
    $category = Category::create(['name' => 'Kuliner', 'slug' => 'kuliner']);
    $owner = User::factory()->create();
    $company = Company::create(['user_id' => $owner->id, 'name' => 'Auth Corp', 'category_id' => $category->id, 'achievement' => '- First']);

    return [
        'company' => $company,
        'actors' => [
            'owner' => [$owner, true],
            'admin' => [User::factory()->admin()->create(), true],
            'stranger' => [User::factory()->create(), false],
        ],
        'payload' => ['name' => 'Auth Corp', 'category_id' => $category->id, 'position' => 'Lead', 'job_description' => 'Memimpin tim penjualan.'],
    ];
}

test('Company::canBeManagedBy allows the owner and admins only', function () {
    $w = companyActors();

    foreach ($w['actors'] as [$user, $allowed]) {
        expect($w['company']->canBeManagedBy($user))->toBe($allowed);
    }
});

test('the work profile form and update answer 200 or 403 by who asks', function () {
    $w = companyActors();

    foreach ($w['actors'] as [$user, $allowed]) {
        $this->actingAs($user)->get(route('intrapreneurs.edit', $w['company']))->assertStatus($allowed ? 200 : 403);
        $this->actingAs($user)->put(route('intrapreneurs.update', $w['company']), $w['payload'])->assertStatus($allowed ? 302 : 403);
    }
});

test('the achievement pages answer 200 or 403 by who asks, with the same JSON message', function () {
    $w = companyActors();

    foreach ($w['actors'] as [$user, $allowed]) {
        $this->actingAs($user)->get(route('intrapreneurs.create_achievement', $w['company']))->assertStatus($allowed ? 200 : 403);

        $add = $this->actingAs($user)->postJson(route('intrapreneurs.add_achievement', $w['company']), ['achievement' => 'Second']);
        $delete = $this->actingAs($user)->deleteJson(route('intrapreneurs.delete_achievement', $w['company']), ['index' => 5]);

        if ($allowed) {
            $add->assertOk();
            $delete->assertOk();
        } else {
            $add->assertForbidden()->assertJson(['message' => 'Unauthorized.']);
            $delete->assertForbidden()->assertJson(['message' => 'Unauthorized.']);
        }
    }
});

test('guests are sent away from the work profile pages', function () {
    $w = companyActors();

    $this->get(route('intrapreneurs.edit', $w['company']))->assertRedirect(route('home'));
    $this->put(route('intrapreneurs.update', $w['company']), $w['payload'])->assertRedirect(route('home'));
    $this->get(route('intrapreneurs.create_achievement', $w['company']))->assertRedirect(route('home'));
});

test('the testimony admin page and its featured toggle are for admins only', function () {
    $student = User::factory()->create(['testimony' => 'Great place to study.']);
    $admin = User::factory()->admin()->create();

    $this->actingAs($student)->get(route('uc-testimonies.admin'))->assertForbidden();
    $this->actingAs($student)->postJson(route('uc-testimonies.toggle-featured', $student))->assertForbidden();

    $this->actingAs($admin)->get(route('uc-testimonies.admin'))->assertOk();
    $this->actingAs($admin)->postJson(route('uc-testimonies.toggle-featured', $student))->assertOk()->assertJson(['success' => true]);
});

test('only owners, co-owners and admins can update a business', function () {
    $category = Category::create(['name' => 'Kuliner', 'slug' => 'kuliner']);
    $owner = User::factory()->create();
    $stranger = User::factory()->create();
    $business = Business::create(['user_id' => $owner->id, 'name' => 'Auth Biz', 'category_id' => $category->id, 'type' => 'entrepreneur']);

    $this->actingAs($stranger)->get(route('businesses.edit', $business))->assertForbidden();
    $this->actingAs($stranger)->put(route('businesses.update', $business), ['name' => 'Auth Biz'])->assertForbidden();
    $this->actingAs($owner)->get(route('businesses.edit', $business))->assertOk();
});
