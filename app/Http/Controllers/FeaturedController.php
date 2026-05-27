<?php

namespace App\Http\Controllers;

use App\Models\Business;
use App\Models\Category;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Class FeaturedController
 *
 * Coordinates and retrieves showcase aggregates for the landing or featured ventures/students dashboard,
 * combining top entrepreneur/intrapreneur students, spotlights, dynamic category counts, and high-scoring AI-approved testimonies.
 */
class FeaturedController extends Controller
{
    /**
     * Display a showcase listing of featured students, ventures, and voices.
     */
    public function index(Request $request): View
    {
        $featuredStudentQuery = fn () => User::query()
            ->where('is_visible', true)
            ->where('is_featured', true)
            ->where('role', '!=', 'admin')
            ->whereNotNull('profile_photo_url');

        // Featured intrapreneur students (admin-starred + intrapreneur status)
        $topIntrapreneurs = $featuredStudentQuery()
            ->whereRaw('LOWER(current_status) = ?', ['intrapreneur'])
            ->with(['companies' => fn ($q) => $q->where('is_visible', true)->with('category')])
            ->latest()
            ->get();

        // Featured entrepreneur students (admin-starred + entrepreneur status)
        $topEntrepreneurs = $featuredStudentQuery()
            ->whereRaw('LOWER(current_status) = ?', ['entrepreneur'])
            ->with(['businesses' => fn ($q) => $q->visible()->entrepreneur()->with('category')])
            ->latest()
            ->get();

        // Spotlight businesses
        $spotlightBusinesses = Business::visible()
            ->entrepreneur()
            ->where('is_featured', true)
            ->latest()
            ->with(['category', 'user'])
            ->get();

        // Categories
        $categories = Category::withCount(['businesses' => fn ($q) => $q->where('is_visible', true)])
            ->orderByDesc('businesses_count')
            ->take(8)
            ->get();

        // Community Voices: users with AI-approved testimonies (NOT tied to is_featured toggle)
        $testimonies = User::where('is_visible', true)
            ->where('is_featured_testimony', true)
            ->whereNotNull('testimony')
            ->where('testimony', '!=', '')
            ->whereNotNull('profile_photo_url')
            ->where(function ($q) {
                // Prefer AI-approved testimonies (score >= 80), but fallback to any visible testimony
                $q->where('ai_score', '>=', 80)
                    ->orWhere(function ($q2) {
                        $q2->whereNull('ai_score')->where('is_visible', true);
                    });
            })
            ->orderByDesc('ai_score')
            ->latest()
            ->take(6)
            ->get();

        return view('featured.index', compact(
            'topIntrapreneurs',
            'topEntrepreneurs',
            'spotlightBusinesses',
            'categories',
            'testimonies'
        ));
    }
}
