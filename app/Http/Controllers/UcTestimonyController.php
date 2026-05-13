<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class UcTestimonyController extends Controller
{
    /**
     * Display the authenticated user's testimony management page.
     */
    public function my(): View
    {
        /** @var User $user */
        $user = Auth::user();
        
        return view('uc-testimonies.my', [
            'user' => $user,
        ]);
    }

    /**
     * Display a public list of testimonies.
     * Note: Removed from main navigation as per user request.
     */
    public function index(): View
    {
        $testimonies = User::query()
            ->where('is_visible', true)
            ->whereNotNull('testimony')
            ->where('testimony', '!=', '')
            ->orderByDesc('submitted_at')
            ->orderByDesc('created_at')
            ->paginate(8);

        return view('uc-testimonies.index', compact('testimonies'));
    }

    /**
     * Store or update the authenticated user's testimony.
     */
    public function store(Request $request): RedirectResponse
    {
        /** @var User $user */
        $user = Auth::user();

        $validated = $request->validate([
            'content' => ['required', 'string', 'min:5', 'max:255'],
        ]);

        $user->forceFill([
            'testimony' => trim($validated['content']),
            'submitted_at' => now(),
        ])->save();

        return redirect()
            ->route('uc-testimonies.my')
            ->with('success', 'Your testimony has been saved successfully!');
    }
}