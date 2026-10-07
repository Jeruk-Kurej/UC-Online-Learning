<?php

use App\Models\User;

test('import check returns the active import id from the session', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    $response = $this->actingAs($admin)
        ->withSession(['active_import' => 'abc-123'])
        ->get('/import-progress/check');

    $response->assertOk();
    $response->assertExactJson(['importId' => 'abc-123']);
});

test('import check returns null when no import is active', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    $response = $this->actingAs($admin)->get('/import-progress/check');

    $response->assertOk();
    $response->assertExactJson(['importId' => null]);
});

test('import progress by id still returns counters', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    $response = $this->actingAs($admin)->get('/import-progress/some-import-id');

    $response->assertOk();
    $response->assertJsonStructure(['status', 'errors', 'total', 'current', 'success', 'skipped']);
});
