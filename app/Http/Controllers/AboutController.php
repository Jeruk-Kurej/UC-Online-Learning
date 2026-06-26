<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Models\Page;

/**
 * Class AboutController
 *
 * Renders the static public details/FAQ about section.
 */
class AboutController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request): View
    {
        $page = Page::where('slug', 'about')->first();
        return view('about', compact('page'));
    }
}
