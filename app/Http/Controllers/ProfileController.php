<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\User;
use App\Services\ImageOptimizerService;
use App\Services\UploadStorage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ProfileController extends Controller
{
    private function getUser(Request $request): User
    {
        $user = $request->user();
        if (! $user instanceof User) {
            abort(401);
        }

        return $user;
    }

    public function edit(Request $request): RedirectResponse|View
    {
        $user = $this->getUser($request);
        if ($user->isAdmin()) {
            return view('profile.account', ['user' => $user]);
        }

        return view('profile.edit', ['user' => $user]);
    }

    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $this->getUser($request);
        $validated = $request->validated();

        $user->fill(collect($validated)->except(['profile_photo', 'cv_file', 'activities_files', 'delete_activities_files', 'certification_files', 'delete_certification_files', 'skills', 'show_contact_details'])->toArray());

        $user->show_contact_details = $request->boolean('show_contact_details');

        if ($request->boolean('delete_profile_photo')) {
            $user->deleteFileFromStorage($user->profile_photo_url);
            $user->profile_photo_url = null;
        }

        if ($request->hasFile('profile_photo')) {
            $user->deleteFileFromStorage($user->profile_photo_url);

            $file = $request->file('profile_photo');
            if ($file instanceof UploadedFile) {
                app(ImageOptimizerService::class)->optimizeUploadedFile($file);
                $slug = Str::slug($user->name, '_');
                $user->profile_photo_url = app(UploadStorage::class)->store($file, 'profile-photos', $slug.'_'.time().'.'.$file->getClientOriginalExtension());
            }
        }

        $activities = $this->syncUploadedFiles($request, $user, $user->activities_doc_url, 'activities_files', 'delete_activities_files', 'student-activities', 'act');
        $user->activities_doc_url = $activities ?: null;

        $certificates = $this->syncUploadedFiles($request, $user, $user->certification_urls, 'certification_files', 'delete_certification_files', 'student-certificates', 'cert');
        $user->expertise_certification_url = $certificates ? implode(';', $certificates) : null;

        $user->save();

        if ($request->has('skills')) {
            $user->syncSkillNames($validated['skills'] ?? null);
        }

        return Redirect::route('profile.edit')->with('success', 'Profile updated!');
    }

    // --- Uploaded documents: drop the ones marked for removal (and their files), then add the newly picked ones ---

    private function syncUploadedFiles(Request $request, User $user, array $existing, string $filesField, string $deleteField, string $folder, string $prefix): array
    {
        $toDelete = (array) $request->input($deleteField, []);
        $kept = [];
        foreach ($existing as $url) {
            if (in_array($url, $toDelete, true)) {
                $user->deleteFileFromStorage($url);
            } else {
                $kept[] = $url;
            }
        }

        $files = $request->file($filesField, []);
        foreach (is_array($files) ? $files : [$files] as $file) {
            if ($file instanceof UploadedFile) {
                $kept[] = app(UploadStorage::class)->store($file, $folder, $prefix.'_'.$user->id.'_'.time().'_'.Str::random(5).'.'.$file->getClientOriginalExtension());
            }
        }

        return $kept;
    }
}
