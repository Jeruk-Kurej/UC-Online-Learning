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

/**
 * Class ProfileController
 *
 * Handles user profile editing, updates, file uploading (profile photos and activity documents),
 * and account deletion processes.
 */
class ProfileController extends Controller
{
    /**
     * Retrieve the currently authenticated user as a typed User instance.
     *
     * @param  Request  $request  Current request instance
     *
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException If unauthenticated
     */
    private function getUser(Request $request): User
    {
        $user = $request->user();
        if (! $user instanceof User) {
            abort(401);
        }

        return $user;
    }

    /**
     * Show the profile edit form.
     *
     * @param  Request  $request  Current request instance
     */
    public function edit(Request $request): RedirectResponse|View
    {
        $user = $this->getUser($request);
        if ($user->isAdmin()) {
            return Redirect::route('users.index');
        }

        return view('profile.edit', ['user' => $user]);
    }

    /**
     * Update the authenticated user's profile details.
     *
     * @param  ProfileUpdateRequest  $request  Form request with validation rules
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $this->getUser($request);
        $validated = $request->validated();

        $user->fill(collect($validated)->except(['profile_photo', 'cv_file', 'activities_files', 'delete_activities_files', 'password', 'show_contact_details'])->toArray());

        $user->show_contact_details = $request->boolean('show_contact_details');

        // Handle profile photo deletion
        if ($request->boolean('delete_profile_photo')) {
            $this->deleteFileFromStorage($user->profile_photo_url);
            $user->profile_photo_url = null;
        }

        if ($request->hasFile('profile_photo')) {
            // Delete old file if exists
            $this->deleteFileFromStorage($user->profile_photo_url);

            $file = $request->file('profile_photo');
            if ($file instanceof \Illuminate\Http\UploadedFile) {
                /** @var \Illuminate\Filesystem\FilesystemAdapter $publicDisk */
                $publicDisk = Storage::disk('public');
                $slug = Str::slug($user->name, '_');
                $path = $file->storeAs('profile-photos', $slug.'_'.time().'.'.$file->getClientOriginalExtension(), 'public');
                if (is_string($path)) {
                    $user->profile_photo_url = $publicDisk->url($path);
                }
            }
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
            /** @var \Illuminate\Filesystem\FilesystemAdapter $publicDisk */
            $publicDisk = Storage::disk('public');
            $files = $request->file('activities_files');
            $files = is_array($files) ? $files : [$files];
            foreach ($files as $file) {
                if ($file instanceof \Illuminate\Http\UploadedFile) {
                    $path = $file->storeAs('student-activities', 'act_'.$user->id.'_'.time().'_'.Str::random(5).'.'.$file->getClientOriginalExtension(), 'public');
                    if (is_string($path)) {
                        $newUrls[] = $publicDisk->url($path);
                    }
                }
            }
        }

        $finalUrls = array_merge($existingUrls, $newUrls);
        $user->activities_doc_url = count($finalUrls) > 0 ? implode(';', $finalUrls) : null;

        if ($request->filled('password')) {
            $user->password = Hash::make($request->input('password'));
        }

        $user->save();

        return Redirect::route('profile.edit')->with('success', 'Profile updated!');
    }

    /**
     * Safely delete a file from local public storage or Cloudinary.
     *
     * @param  string|null  $pathOrUrl  Relative storage path or external URL
     */
    private function deleteFileFromStorage(?string $pathOrUrl): void
    {
        if (! $pathOrUrl) {
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
            $relativePath = (string) (parse_url($relativePath, PHP_URL_PATH) ?? $relativePath);
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

    /**
     * Delete the user's account.
     *
     * @param  Request  $request  Current request instance
     */
    public function destroy(Request $request): RedirectResponse
    {
        $this->validate($request, ['password' => ['required', 'current_password']]);
        $user = $this->getUser($request);
        Auth::logout();
        $user->delete();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
