<?php

use App\Models\User;

test('profile page is displayed', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->get('/profile');

    $response->assertOk();
});

test('profile information can be updated', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->patch('/profile', [
            'name' => 'Test User',
            'whatsapp' => '0812345678',
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect('/profile');

    $user->refresh();

    $this->assertSame('Test User', $user->name);
    $this->assertSame('0812345678', $user->whatsapp);
});

test('users cannot delete their own account', function () {
    $user = User::factory()->create(['password' => Hash::make('password')]);

    $this->actingAs($user)->delete('/profile', ['password' => 'password'])->assertStatus(405);

    $this->assertNotNull($user->fresh());
});
