<?php

use App\Models\Company;
use App\Models\User;

test('company owner can add an achievement', function () {
    $owner = User::factory()->create();
    $company = Company::create([
        'user_id'   => $owner->id,
        'name'      => 'Test Corp',
        'slug'      => 'test-corp',
        'is_visible' => true,
        'achievement' => null,
    ]);

    $response = $this->actingAs($owner)
        ->postJson(route('intrapreneurs.add_achievement', $company), [
            'achievement' => 'Won National Innovation Award 2026',
        ]);

    $response->assertOk()
             ->assertJson(['success' => true]);

    $this->assertDatabaseHas('companies', [
        'id' => $company->id,
    ]);

    $company->refresh();
    $this->assertStringContainsString('Won National Innovation Award 2026', $company->achievement);
});

test('company owner can view add achievement form page', function () {
    $owner = User::factory()->create();
    $company = Company::create([
        'user_id'   => $owner->id,
        'name'      => 'Test Corp Form',
        'slug'      => 'test-corp-form',
        'is_visible' => true,
        'achievement' => null,
    ]);

    $response = $this->actingAs($owner)
        ->get(route('intrapreneurs.create_achievement', $company));

    $response->assertStatus(200)
             ->assertSee('Add New Achievement')
             ->assertSee('Test Corp Form');
});

test('non-owner cannot view add achievement form page', function () {
    $owner = User::factory()->create();
    $nonOwner = User::factory()->create();
    $company = Company::create([
        'user_id'   => $owner->id,
        'name'      => 'Test Corp Form',
        'slug'      => 'test-corp-form',
        'is_visible' => true,
        'achievement' => null,
    ]);

    $response = $this->actingAs($nonOwner)
        ->get(route('intrapreneurs.create_achievement', $company));

    $response->assertStatus(403);
});

test('company owner can add an achievement via form redirect', function () {
    $owner = User::factory()->create();
    $company = Company::create([
        'user_id'   => $owner->id,
        'name'      => 'Test Corp Redirect',
        'slug'      => 'test-corp-redirect',
        'is_visible' => true,
        'achievement' => null,
    ]);

    $response = $this->actingAs($owner)
        ->post(route('intrapreneurs.add_achievement', $company), [
            'achievement' => 'Won National Innovation Award 2026 via Form',
        ]);

    $response->assertRedirect(route('intrapreneurs.show', $company));

    $company->refresh();
    $this->assertStringContainsString('Won National Innovation Award 2026 via Form', $company->achievement);
});

test('company owner can delete an achievement', function () {
    $owner = User::factory()->create();
    $company = Company::create([
        'user_id'   => $owner->id,
        'name'      => 'Test Corp Two',
        'slug'      => 'test-corp-two',
        'is_visible' => true,
        'achievement' => "- First Achievement\n- Second Achievement",
    ]);

    $response = $this->actingAs($owner)
        ->deleteJson(route('intrapreneurs.delete_achievement', $company), [
            'index' => 0,
        ]);

    $response->assertOk()
             ->assertJson(['success' => true]);

    $company->refresh();
    $this->assertStringNotContainsString('First Achievement', $company->achievement ?? '');
    $this->assertStringContainsString('Second Achievement', $company->achievement ?? '');
});

test('non-owner cannot add or delete achievements', function () {
    $owner    = User::factory()->create();
    $nonOwner = User::factory()->create();
    $company  = Company::create([
        'user_id'   => $owner->id,
        'name'      => 'Test Corp Three',
        'slug'      => 'test-corp-three',
        'is_visible' => true,
        'achievement' => '- Some Achievement',
    ]);

    $addResponse = $this->actingAs($nonOwner)
        ->postJson(route('intrapreneurs.add_achievement', $company), [
            'achievement' => 'Hacked Achievement',
        ]);
    $addResponse->assertStatus(403);

    $deleteResponse = $this->actingAs($nonOwner)
        ->deleteJson(route('intrapreneurs.delete_achievement', $company), [
            'index' => 0,
        ]);
    $deleteResponse->assertStatus(403);
});

test('achievement parser strips bullet prefixes correctly', function () {
    $company = new Company();
    $company->achievement = "- First Item\n- Second Item\n- Third Item";

    $list = $company->achievements_list;

    expect($list)->toHaveCount(3)
                 ->sequence(
                     fn ($item) => $item->toBe('First Item'),
                     fn ($item) => $item->toBe('Second Item'),
                     fn ($item) => $item->toBe('Third Item'),
                 );
});

test('achievement parser handles empty achievement gracefully', function () {
    $company = new Company();
    $company->achievement = null;

    expect($company->achievements_list)->toBe([]);
});
