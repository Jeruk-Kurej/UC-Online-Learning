<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class UcTestimonyController extends Controller
{
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

    public function store(Request $request): RedirectResponse
    {
        /** @var User|null $user */
        $user = Auth::user();

        if (! $user) {
            return redirect()
                ->route('login')
                ->with('error', 'Please sign in before submitting a testimony.');
        }

        if ($user->isAdmin()) {
            abort(403, 'Administrators cannot submit testimonies.');
        }

        $validated = $request->validate([
            'rating' => ['required', 'integer', 'between:1,5'],
            'content' => ['required', 'string', 'min:20'],
        ]);

        $user->forceFill([
            'testimony' => trim($validated['content']),
            'submitted_at' => now(),
            'is_visible' => true,
        ])->save();

        return redirect()
            ->route('uc-testimonies.index')
            ->with('success', 'Thanks! Your testimony has been saved and will appear on the public page.');
    }
}