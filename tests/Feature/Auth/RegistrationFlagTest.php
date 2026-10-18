<?php

use Illuminate\Support\Facades\Route;

// --- The registration switch is a config value (auth.registration_open), read from REGISTRATION_OPEN in config/auth.php ---

function bootWithRegistration(string $value): void
{
    $previous = [$_ENV['REGISTRATION_OPEN'] ?? null, $_SERVER['REGISTRATION_OPEN'] ?? null, getenv('REGISTRATION_OPEN')];
    $_ENV['REGISTRATION_OPEN'] = $_SERVER['REGISTRATION_OPEN'] = $value;
    putenv("REGISTRATION_OPEN={$value}");

    test()->beforeApplicationDestroyed(function () use ($previous) {
        [$_ENV['REGISTRATION_OPEN'], $_SERVER['REGISTRATION_OPEN']] = [$previous[0], $previous[1]];
        putenv('REGISTRATION_OPEN='.($previous[2] === false ? '' : $previous[2]));
    });

    test()->refreshApplication();
}

test('the test environment opens registration through the config value', function () {
    expect(config('auth.registration_open'))->toBeTrue();
});

test('registration is closed by default: no register routes, the login page still works', function () {
    bootWithRegistration('false');

    expect(config('auth.registration_open'))->toBeFalse()
        ->and(Route::has('register'))->toBeFalse()
        ->and(collect(Route::getRoutes()->getRoutes())->filter(fn ($route) => $route->uri() === 'register')->isEmpty())->toBeTrue();
    $this->get('/login')->assertOk();
});

test('registration opens again when the switch is true at boot', function () {
    bootWithRegistration('true');

    expect(config('auth.registration_open'))->toBeTrue()
        ->and(Route::has('register'))->toBeTrue();
});
