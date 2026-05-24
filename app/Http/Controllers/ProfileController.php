<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\User;
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

        $user->fill(collect($validated)->except(['profile_photo', 'cv_file', 'activities_files', 'delete_activities_files', 'password'])->toArray());

        // Handle profile photo deletion
        if ($request->boolean('delete_profile_photo')) {
            $this->deleteFileFromStorage($user->profile_photo_url);
            $user->profile_photo_url = null;
        }

        if ($request->hasFile('profile_photo')) {
            // Delete old file if exists
            $this->deleteFileFromStorage($user->profile_photo_url);
            
            $file = $request->file('profile_photo');
            $slug = Str::slug($user->name, '_');
            $path = $file->storeAs('profile-photos', $slug . '_' . time() . '.' . $file->getClientOriginalExtension(), 'public');
            $user->profile_photo_url = Storage::disk('public')->url($path);
        }

        // Handle activities files deletion and uploading
        $existingUrls = [];
        if ($user->activities_doc_url) {
            $existingUrls = array_filter(array_map('trim', preg_split('/[;,]+/', $user->activities_doc_url)));
        }

        // Delete flagged URLs
        if ($request->has('delete_activities_files')) {
            $toDelete = (array) $request->input('delete_activities_files');
            foreach ($toDelete as $urlToDelete) {
                if (($key = array_search($urlToDelete, $existingUrls)) !== false) {
                    $this->deleteFileFromStorage($urlToDelete);
                    unset($existingUrls[$key]);
                }
            }
            $existingUrls = array_values($existingUrls);
        }

        // Upload new files
        $newUrls = [];
        if ($request->hasFile('activities_files')) {
            foreach ($request->file('activities_files') as $file) {
                $path = $file->storeAs('student-activities', 'act_' . $user->id . '_' . time() . '_' . Str::random(5) . '.' . $file->getClientOriginalExtension(), 'public');
                $newUrls[] = Storage::disk('public')->url($path);
            }
        }

        $finalUrls = array_merge($existingUrls, $newUrls);
        $user->activities_doc_url = count($finalUrls) > 0 ? implode(';', $finalUrls) : null;

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return Redirect::route('profile.edit')->with('success', 'Profile updated!');
    }

    /**
     * Safely delete a file from local public storage or Cloudinary.
     */
    private function deleteFileFromStorage(?string $pathOrUrl): void
    {
        if (!$pathOrUrl) {
            return;
        }

        // Handle Cloudinary URL
        if (str_contains($pathOrUrl, 'cloudinary.com')) {
            try {
                User::deleteCloudinaryImage($pathOrUrl);
            } catch (\Throwable $e) {
                // silently swallow
            }
            return;
        }

        // Normalize local storage path
        $relativePath = $pathOrUrl;
        if (str_starts_with($relativePath, 'http://') || str_starts_with($relativePath, 'https://')) {
            $relativePath = parse_url($relativePath, PHP_URL_PATH);
        }

        if (str_starts_with($relativePath, '/storage/')) {
            $relativePath = substr($relativePath, strlen('/storage/'));
        } elseif (str_starts_with($relativePath, 'storage/')) {
            $relativePath = substr($relativePath, strlen('storage/'));
        }

        $relativePath = ltrim($relativePath, '/');

        if (Storage::disk('public')->exists($relativePath)) {
            Storage::disk('public')->delete($relativePath);
        }
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
