<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

/**
 * Class AiAnalysisController
 *
 * Handles admin-only views and status toggles for monitoring AI-scored/moderated testimonies.
 */
class AiAnalysisController extends Controller
{
    /**
     * Display a listing of testimonies (Admin only - for monitoring).
     */
    public function index(): View
    {
        $testimonies = User::query()
            ->whereNotNull('testimony')
            ->where('testimony', '!=', '')
            ->orderByDesc('submitted_at')
            ->orderByDesc('created_at')
            ->paginate(20);

        // Calculate stats
        $totalCount = $testimonies->total();
        $approvedCount = User::whereNotNull('testimony')->where('testimony', '!=', '')->where('is_visible', true)->count();
        $rejectedCount = User::whereNotNull('testimony')->where('testimony', '!=', '')->where('is_visible', false)->count();
        $approvalRate = $totalCount > 0 ? round(($approvedCount / $totalCount) * 100, 1) : 0;

        return view('ai-analyses.index', compact('testimonies', 'totalCount', 'approvedCount', 'rejectedCount', 'approvalRate'));
    }

    /**
     * Toggle testimony visibility (Admin only).
     */
    public function toggle(User $user): RedirectResponse
    {
        $user->is_visible = ! $user->is_visible;
        $user->save();

        $status = $user->is_visible ? 'approved and is now visible' : 'rejected (hidden)';

        return back()->with('success', "Success! The testimony from '{$user->name}' has been {$status}.");
    }

    /**
     * Display a specific testimony detail (Admin only).
     */
    public function show(User $user): View
    {
        return view('ai-analyses.show', compact('user'));
    }
}
