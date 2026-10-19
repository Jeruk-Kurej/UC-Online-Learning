<?php

use App\Models\User;

test('admins get an account page with the password card instead of a redirect', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)->get('/profile')->assertOk()->assertSee('Change password')->assertDontSee('Studies');
});

test('students see the password card under their profile form', function () {
    $this->actingAs(User::factory()->create())->get('/profile')->assertOk()->assertSee('Change password')->assertSee('Studies');
});

test('changing the password shows a normal message', function () {
    $user = User::factory()->create(['password' => Hash::make('old-password-1')]);

    $this->actingAs($user)->from('/profile')->put('/password', [
        'current_password' => 'old-password-1',
        'password' => 'new-password-22',
        'password_confirmation' => 'new-password-22',
    ])->assertRedirect('/profile')->assertSessionHas('success', 'Your password has been changed.');
});

test('the login page does not link to a password reset until e-mail is set up', function () {
    $this->get('/login')->assertOk()->assertDontSee('forgot-password', false)->assertDontSee('Forgot');
});
