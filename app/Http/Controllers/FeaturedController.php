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

        // Testimonies (curated testimonies, with photo and testimony)
        $testimonies = User::where('is_visible', true)
            ->where('is_featured', true)
            ->whereNotNull('testimony')
            ->where('testimony', '!=', '')
            ->whereNotNull('profile_photo_url')
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
