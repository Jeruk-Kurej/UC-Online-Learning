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
        // Featured profiles (Must have is_featured = true)
        $topProfiles = User::where('is_visible', true)
            ->where('is_featured', true)
            ->with(['businesses' => fn ($q) => $q->where('is_visible', true)->with('category')])
            ->with(['memberOfBusinesses' => fn ($q) => $q->where('is_visible', true)->with('category')])
            ->latest()
            ->take(8)
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

        // Testimonies (students only, with photo)
        $testimonies = User::where('is_visible', true)
            ->whereNotNull('testimony')
            ->where('testimony', '!=', '')
            ->whereNotNull('profile_photo_url')
            ->with(['businesses' => fn ($q) => $q->where('is_visible', true)->take(1)])
            ->latest()
            ->take(6)
            ->get();

        return view('featured.index', [
            'topProfiles' => $topProfiles,
            'spotlightBusinesses' => $spotlightBusinesses,
            'categories' => $categories,
            'testimonies' => $testimonies,
        ]);
    }
}
