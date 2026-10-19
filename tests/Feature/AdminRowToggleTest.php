<?php

use App\Models\User;
use Illuminate\Support\Facades\DB;

// --- Hide/Show and Feature answer JSON for the buttons that change a row without a reload ---

test('hiding a user answers with the new state and un-features them', function () {
    $admin = User::factory()->admin()->create();
    $user = User::factory()->create(['is_visible' => true, 'is_featured' => true]);

    $this->actingAs($admin)->postJson(route('users.toggle-status', $user))
        ->assertOk()
        ->assertJson(['success' => true, 'is_visible' => false, 'is_featured' => false]);

    $this->actingAs($admin)->postJson(route('users.toggle-status', $user))
        ->assertOk()
        ->assertJson(['success' => true, 'is_visible' => true, 'is_featured' => false]);
});

test('featuring a hidden user is refused with a message the page can show', function () {
    $admin = User::factory()->admin()->create();
    $user = User::factory()->create(['is_visible' => false, 'is_featured' => false]);

    $this->actingAs($admin)->postJson(route('users.toggle-featured', $user))
        ->assertStatus(422)
        ->assertJson(['success' => false])
        ->assertJsonStructure(['message']);
});

test('the user rows carry what the in place buttons need', function () {
    DB::connection()->getPdo()->sqliteCreateFunction('REGEXP', fn ($pattern, $value) => preg_match('/'.$pattern.'/', (string) $value) ? 1 : 0, 2);
    $admin = User::factory()->admin()->create();
    User::factory()->create(['is_visible' => true]);

    $html = $this->actingAs($admin)->get(route('users.index'))->getContent();

    expect($html)->toContain('data-user-row')
        ->and($html)->toContain('data-row-toggle="status"')
        ->and($html)->toContain('data-row-toggle="featured"')
        ->and($html)->toContain('data-visibility-badge')
        ->and($html)->toContain('data-status-button');
});
