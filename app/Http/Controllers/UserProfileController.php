<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Support\GoogleDrive;

/**
 * Public profile pages of students and alumni.
 */
class UserProfileController extends Controller
{
    /**
     * Display the specified user profile catalog.
     */
    public function show($user)
    {
        if (! $user instanceof User) {
            $user = User::where('slug', $user)->orWhere('id', $user)->firstOrFail();
        }

        $user->load([
            'businesses' => function ($query) {
                $query->where('is_visible', true)->with('category');
            },
            'memberOfBusinesses' => function ($query) use ($user) {
                $query->where('is_visible', true)
                    ->where('businesses.user_id', '!=', $user->id)
                    ->with('category');
            },
            'skills',
            'companies.category',
        ]);

        return view('users.show', [
            'user' => $user,
            'collabStatus' => auth()->check() && auth()->id() !== $user->id ? auth()->user()->collabStatusWith($user) : null,
        ]);
    }

    /**
     * Proxy public Google Drive image/file download to bypass browser cookies and CORS issues.
     */
    public function proxyGoogleDriveImage($id)
    {
        return redirect(GoogleDrive::viewUrl($id));
    }
}
