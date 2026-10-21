<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Support\StartingPassword;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class PasswordController extends Controller
{
    /**
     * Update the user's password.
     */
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validateWithBag('updatePassword', [
            'current_password' => self::needsCurrentPassword() ? ['required', 'current_password'] : ['nullable'],
            'password' => ['required', Password::defaults(), 'confirmed', function (string $attribute, string $value, \Closure $fail) {
                if (StartingPassword::matches($value)) {
                    $fail('Choose a password of your own, not the starting password.');
                }
            }],
        ]);

        /** @var User $user */
        $user = $request->user();
        $user->password = Hash::make($validated['password']);
        $user->save();

        $request->session()->forget('signed_in_with_google');

        return back()->with('success', 'Your password has been changed.');
    }

    /**
     * Someone who just signed in with Google proved the email address, so they may set a password without the current one (imported accounts never learn theirs).
     */
    public static function needsCurrentPassword(): bool
    {
        return ! session('signed_in_with_google', false);
    }
}
