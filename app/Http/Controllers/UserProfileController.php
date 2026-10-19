<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Support\GoogleDrive;

/**
 * Public profile pages of students and alumni.
 */
class UserProfileController extends Controller
{
    public function show($user)
    {
        if (! $user instanceof User) {
            $user = User::where('slug', $user)->orWhere('id', $user)->firstOrFail();
        }

        // --- A hidden person, or an admin account, has no public profile; the person and admins still open it ---
        $viewer = auth()->user();
        abort_unless(($user->is_visible && ! $user->isAdmin()) || $viewer?->is($user) || $viewer?->isAdmin(), 404);

        // --- The lists show what the public may open: approved and visible ventures only ---
        $user->load([
            'businesses' => fn ($query) => $query->visible()->with('category'),
            'memberOfBusinesses' => fn ($query) => $query->visible()->where('businesses.user_id', '!=', $user->id)->with('category'),
            'skills',
            'companies' => fn ($query) => $query->visible()->with('category'),
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
