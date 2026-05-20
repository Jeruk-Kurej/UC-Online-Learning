<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function edit(Request $request): RedirectResponse|View
    {
        if ($request->user()->isAdmin()) {
            return Redirect::route('users.index');
        }
        return view('profile.edit', ['user' => $request->user()]);
    }

    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        $validated = $request->validated();

        $user->fill(collect($validated)->except(['profile_photo', 'cv_file', 'activities_file', 'password'])->toArray());

        if ($request->hasFile('profile_photo')) {
            $file = $request->file('profile_photo');
            $slug = Str::slug($user->name, '_');
            $path = $file->storeAs('profile-photos', $slug . '_' . time() . '.' . $file->getClientOriginalExtension(), 'public');
            $user->profile_photo_url = Storage::disk('public')->url($path);
        }



        if ($request->hasFile('activities_file')) {
            $file = $request->file('activities_file');
            $path = $file->storeAs('student-activities', 'act_' . $user->id . '_' . time() . '.' . $file->getClientOriginalExtension(), 'public');
            $user->activities_doc_url = Storage::disk('public')->url($path);
        }

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return Redirect::route('profile.edit')->with('success', 'Profile updated!');
    }

    public function destroy(Request $request): RedirectResponse
    {
        $request->validate(['password' => ['required', 'current_password']]);
        $user = $request->user();
        Auth::logout();
        $user->delete();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return Redirect::to('/');
    }
}
