<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\User;
use App\Services\ImageOptimizerService;
use App\Services\UploadStorage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
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

        $user->fill(collect($validated)->except(['profile_photo', 'cv_file', 'activities_files', 'delete_activities_files', 'password', 'show_contact_details'])->toArray());

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

        $existingUrls = $user->activities_doc_url;

        if ($request->has('delete_activities_files')) {
            $toDelete = (array) $request->input('delete_activities_files');
            foreach ($toDelete as $urlToDelete) {
                if (($key = array_search($urlToDelete, $existingUrls)) !== false) {
                    $user->deleteFileFromStorage($urlToDelete);
                    unset($existingUrls[$key]);
                }
            }
            $existingUrls = array_values($existingUrls);
        }

        $newUrls = [];
        if ($request->hasFile('activities_files')) {
            $files = $request->file('activities_files');
            $files = is_array($files) ? $files : [$files];
            foreach ($files as $file) {
                if ($file instanceof UploadedFile) {
                    $newUrls[] = app(UploadStorage::class)->store($file, 'student-activities', 'act_'.$user->id.'_'.time().'_'.Str::random(5).'.'.$file->getClientOriginalExtension());
                }
            }
        }

        $finalUrls = array_merge($existingUrls, $newUrls);
        $user->activities_doc_url = $finalUrls ?: null;

        if ($request->filled('password')) {
            $user->password = Hash::make($request->input('password'));
        }

        $user->save();

        return Redirect::route('profile.edit')->with('success', 'Profile updated!');
    }
}
