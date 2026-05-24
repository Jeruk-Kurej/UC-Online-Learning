<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class GoogleAuthController extends Controller
{
    public function redirect(): RedirectResponse
    {
        return Socialite::driver('google')->redirect();
    }

    public function callback(Request $request): RedirectResponse
    {
        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (\Throwable $e) {
            return redirect()->route('login')->with('error', 'Google sign-in failed. Please try again.');
        }

        $email = $googleUser->getEmail();

        if (! $email) {
            return redirect()->route('login')->with('error', 'Your Google account does not have an email address.');
        }

        $user = User::where('email', $email)->first();

        if (! $user) {
            return redirect()->route('login')->with('error', 'No account found for this email. Please contact the admin.');
        }

        if ($user->google_id !== $googleUser->getId()) {
            $user->google_id = $googleUser->getId();
            $user->save();
        }

        Auth::login($user, true);
        $request->session()->regenerate();

        return redirect()->route('home');
    }
}
