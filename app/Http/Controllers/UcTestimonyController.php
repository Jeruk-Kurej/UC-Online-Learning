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
     * Display a listing of testimonies for admin management.
     */
    public function adminIndex(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();
        if (!$user || !$user->isAdmin()) {
            abort(403, 'Only administrators can view testimonies management.');
        }

        $search = trim((string) $request->get('search', ''));
        $featured = $request->get('featured');

        $query = User::query()
            ->whereNotNull('testimony')
            ->where('testimony', '!=', '');

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                    ->orWhere('testimony', 'LIKE', "%{$search}%");
            });
        }

        if ($featured === '1') {
            $query->where('is_featured_testimony', true);
        } elseif ($featured === '0') {
            $query->where('is_featured_testimony', false);
        }

        $users = $query->orderByDesc('submitted_at')
            ->orderByDesc('created_at')
            ->paginate(12);

        // Fetch stats
        $totalTestimonies = User::whereNotNull('testimony')->where('testimony', '!=', '')->count();
        $featuredTestimonies = User::whereNotNull('testimony')->where('testimony', '!=', '')->where('is_featured_testimony', true)->count();

        if ($request->ajax()) {
            return response()
                ->view('uc-testimonies.partials.list', compact('users', 'search'))
                ->header('Vary', 'X-Requested-With');
        }

        return view('uc-testimonies.admin', compact('users', 'totalTestimonies', 'featuredTestimonies'));
    }

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

    /**
     * Toggle the featured status of a testimony for the homepage.
     */
    public function toggleFeatured(Request $request, User $user)
    {
        if (!Auth::user()->isAdmin()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        if (!$user->is_visible) {
            return response()->json(['success' => false, 'message' => 'Cannot feature an invisible user.'], 400);
        }

        $user->is_featured_testimony = !$user->is_featured_testimony;
        $user->save();

        return response()->json([
            'success' => true,
            'is_featured' => $user->is_featured_testimony,
            'message' => 'Testimony featured status updated successfully.'
        ]);
    }
}