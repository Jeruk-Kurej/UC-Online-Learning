<?php

namespace App\Http\Controllers;

use App\Models\User;

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
        // Publicly accessible catalog - removed admin guard

        // Load relationships
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
        ]);
    }

    /**
     * Proxy public Google Drive image/file download to bypass browser cookies and CORS issues.
     */
    public function proxyGoogleDriveImage($id)
    {
        $url = 'https://drive.google.com/uc?export=view&id=' . $id;
        return redirect($url);
    }
}
