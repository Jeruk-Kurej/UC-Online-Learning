<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class DashboardController extends Controller
{
    /**
     * Display the administrative dashboard.
     */
    public function index(Request $request)
    {
        if (!auth()->user()->isAdmin()) {
            return app(FeaturedController::class)->index($request);
        }

        $stats = [
            'total_users' => \App\Models\User::count(),
            'total_businesses' => \App\Models\Business::count(),
            'total_companies' => \App\Models\Company::count(),
            'pending_visibility' => \App\Models\Business::where('is_visible', false)->count(),
        ];

        $recentUsers = \App\Models\User::latest()->take(5)->get();
        $recentBusinesses = \App\Models\Business::with('category')->latest()->take(5)->get();

        return view('dashboard', compact('stats', 'recentUsers', 'recentBusinesses'));
    }

    /**
     * Track import progress by session ID.
     */
    public function importProgress($importId)
    {
        $prefix = "import_{$importId}";
        
        // Fetch status and errors from the shared cache (Database)
        $progress = Cache::get($prefix);

        if (!$progress) {
             // Fallback to ensure UI has something to work with initially
             $progress = [
                'status' => 'processing',
                'errors' => []
            ];
        }

        // Fetch atomic counts pushed by the background worker
        $total = (int) Cache::get("{$prefix}_total", 0);
        $current = (int) Cache::get("{$prefix}_current", 0);
        $success = (int) Cache::get("{$prefix}_success", 0);
        $skipped = (int) Cache::get("{$prefix}_skipped", 0);

        $progress['total'] = $total;
        $progress['current'] = $current;
        $progress['success'] = $success;
        $progress['skipped'] = $skipped;

        return response()->json($progress);
    }

    /**
     * Check if there's an active import and return its ID.
     */
    public function checkActiveImport()
    {
        // Check all possible session keys where import IDs might be stored
        $importId = session('active_import') 
            ?: session('importId') 
            ?: session('active_business_import_id') 
            ?: session('active_user_import_id');
        
        if ($importId) {
            return response()->json(['importId' => $importId]);
        }
        
        return response()->json(['importId' => null]);
    }

    /**
     * Clear the active import session after completion or manual dismiss.
     */
    public function clearActiveImport(Request $request)
    {
        // Clear all possible session keys used for imports
        session()->forget([
            'active_import', 
            'importId', 
            'active_business_import_id', 
            'active_user_import_id'
        ]);
        return response()->json(['status' => 'cleared']);
    }
}
