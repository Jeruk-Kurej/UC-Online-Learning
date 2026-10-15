<?php

namespace App\Http\Controllers;

use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\View\View;

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
        $sections = Page::sectionsFor($page);

        return view('about', compact('page', 'sections'));
    }
}
