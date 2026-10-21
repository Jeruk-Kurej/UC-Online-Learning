<?php

namespace App\Support;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * The password an imported account starts with. It is shared by many accounts and may be known to others,
 * so it is never accepted at the login form: the person signs in with Google and then sets their own.
 */
class StartingPassword
{
    public static function hash(): string
    {
        return Hash::make(self::value() ?? Str::random(40));
    }

    public static function matches(?string $password): bool
    {
        $value = self::value();

        return $value !== null && $password !== null && hash_equals($value, $password);
    }

    private static function value(): ?string
    {
        $value = config('accounts.starting_password');

        return is_string($value) && $value !== '' ? $value : null;
    }
}
