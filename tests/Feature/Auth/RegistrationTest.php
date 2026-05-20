<?php

test('registration screen can be rendered', function () {
    putenv('REGISTRATION_OPEN=true');
    $response = $this->get('/register');

    $response->assertStatus(200);
});

test('new users can register', function () {
    $this->withoutExceptionHandling();
    putenv('REGISTRATION_OPEN=true');
    $response = $this->post('/register', [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect(route('featured', absolute: false));
});

