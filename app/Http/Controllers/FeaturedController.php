<?php

namespace App\Http\Controllers;

use App\Models\Business;
use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FeaturedController extends Controller
{
    public function index(Request $request): View
    {
        $spotlight = Business::visible()
            ->entrepreneur()
            ->where('is_featured', true)
            ->newestFirst()
            ->with(['category', 'user', 'members', 'products'])
            ->get();

        return view('featured.index', [
            'topIntrapreneurs' => $this->featuredStudents('intrapreneur')
                ->with(['companies' => fn ($q) => $q->where('is_visible', true)->with('category')])
                ->newestFirst()
                ->get(),
            'topEntrepreneurs' => $this->featuredStudents('entrepreneur')
                ->with(['businesses' => fn ($q) => $q->visible()->entrepreneur()->with('category')])
                ->newestFirst()
                ->get(),
            'heroVenture' => $spotlight->first(),
            'moreVentures' => $spotlight->slice(1)->values(),
            'categories' => Category::withCount(['businesses' => fn ($q) => $q->where('is_visible', true)])
                ->orderByDesc('businesses_count')
                ->orderBy('name')
                ->orderBy('id')
                ->take(8)
                ->get(),
            'testimonies' => $this->communityTestimonies(),
        ]);
    }

    // --- Home page sections ---

    /**
     * Admin-starred, visible students with a photo whose career status is $status.
     */
    private function featuredStudents(string $status): Builder
    {
        return User::query()
            ->where('is_visible', true)
            ->where('is_featured', true)
            ->where('role', '!=', 'admin')
            ->whereNotNull('profile_photo_url')
            ->whereRaw('LOWER(current_status) = ?', [$status]);
    }

    /**
     * Testimonies picked for the home page (not tied to is_featured): AI-approved ones (score >= 80) first, then any
     * visible testimony that was never scored.
     */
    private function communityTestimonies(): Collection
    {
        return User::where('is_visible', true)
            ->where('is_featured_testimony', true)
            ->withTestimony()
            ->whereNotNull('profile_photo_url')
            ->where(function ($q) {
                $q->where('ai_score', '>=', 80)
                    ->orWhere(function ($q2) {
                        $q2->whereNull('ai_score')->where('is_visible', true);
                    });
            })
            ->orderByDesc('ai_score')
            ->newestFirst()
            ->take(6)
            ->get();
    }
}
