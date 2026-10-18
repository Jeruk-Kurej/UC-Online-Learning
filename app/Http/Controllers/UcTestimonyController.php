<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class UcTestimonyController extends Controller
{
    public function adminIndex(Request $request): View|Response
    {
        $search = trim((string) $request->get('search', ''));
        $featured = $request->get('featured');

        $query = User::withTestimony();

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

        $totalTestimonies = User::withTestimony()->count();
        $featuredTestimonies = User::withTestimony()->where('is_featured_testimony', true)->count();

        if ($request->ajax()) {
            return response()
                ->view('uc-testimonies.partials.list', compact('users', 'search'))
                ->header('Vary', 'X-Requested-With');
        }

        return view('uc-testimonies.admin', compact('users', 'totalTestimonies', 'featuredTestimonies'));
    }

    public function my(): View
    {
        /** @var User $user */
        $user = Auth::user();

        return view('uc-testimonies.my', [
            'user' => $user,
        ]);
    }

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

    public function toggleFeatured(Request $request, User $user): JsonResponse
    {
        if (! $user->is_visible) {
            return new JsonResponse(['success' => false, 'message' => 'Cannot feature an invisible user.'], 400);
        }

        $user->is_featured_testimony = ! $user->is_featured_testimony;
        $user->save();

        return new JsonResponse([
            'success' => true,
            'is_featured' => $user->is_featured_testimony,
            'message' => 'Testimony featured status updated successfully.',
        ]);
    }
}
