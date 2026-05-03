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
        // Top 3 entrepreneur profiles (have business + photo + testimony)
        $topEntrepreneurs = User::where('is_visible', true)
            ->whereHas('businesses', fn ($q) => $q->where('type', 'entrepreneur')->where('is_visible', true))
            ->whereNotNull('profile_photo_url')
            ->whereNotNull('testimony')
            ->with(['businesses' => fn ($q) => $q->where('type', 'entrepreneur')->where('is_visible', true)->with('category')])
            ->latest()
            ->take(3)
            ->get();

        // Top 5 intrapreneur profiles (have company)
        $topIntrapreneurs = User::where('is_visible', true)
            ->whereHas('companies', fn ($q) => $q->where('is_visible', true))
            ->with(['companies' => fn ($q) => $q->where('is_visible', true)->with('category')])
            ->latest()
            ->take(5)
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
        $businessTypes = Category::withCount(['businesses' => fn ($q) => $q->where('is_visible', true)])
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

        return view('featured.index', compact(
            'topEntrepreneurs',
            'topIntrapreneurs',
            'spotlightBusinesses',
            'businessTypes',
            'testimonies',
        ));
    }
}
