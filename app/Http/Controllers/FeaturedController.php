<?php

namespace App\Http\Controllers;

use App\Models\Business;
use App\Models\Company;
use App\Models\Category;
use App\Models\User;
use Illuminate\Http\Request;

class FeaturedController extends Controller
{
    public function index(Request $request)
    {
        // Top 3 featured entrepreneur student profiles (have photo)
        $topEntrepreneurs = User::where('is_visible', true)
            ->where('is_featured', true)
            ->where('current_status', 'Entrepreneur')
            ->whereNotNull('profile_photo_url')
            ->with(['businesses' => fn ($q) => $q->where('type', 'entrepreneur')->where('is_visible', true)->with('category')])
            ->latest()
            ->take(3)
            ->get();

        // Top 3 featured intrapreneur student profiles (have photo)
        $topIntrapreneurs = User::where('is_visible', true)
            ->where('is_featured', true)
            ->where('current_status', 'Intrapreneur')
            ->whereNotNull('profile_photo_url')
            ->with(['companies' => fn ($q) => $q->where('is_visible', true)->with('category')])
            ->latest()
            ->take(3)
            ->get();

        // Spotlight businesses
        $spotlightBusinesses = Business::visible()
            ->entrepreneur()
            ->where('is_featured', true)
            ->latest()
            ->with(['category', 'user'])
            ->take(4)
            ->get();

        // Categories
        $categories = Category::withCount(['businesses' => fn ($q) => $q->where('is_visible', true)])
            ->orderByDesc('businesses_count')
            ->take(8)
            ->get();

        // Community Voices: users with AI-approved testimonies (NOT tied to is_featured toggle)
        // is_featured only controls Featured Students + Featured Ventures sections above.
        $testimonies = User::where('is_visible', true)
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
            'topEntrepreneurs',
            'topIntrapreneurs',
            'spotlightBusinesses',
            'categories',
            'testimonies',
        ));
    }
}
