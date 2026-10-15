<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Http\Request;

class PageController extends Controller
{
    public function edit(Page $page)
    {
        $initialSections = Page::sectionsFor($page);

        return view('admin.pages.edit', compact('page', 'initialSections'));
    }

    public function update(Request $request, Page $page)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'content_json' => 'nullable|array',
        ]);

        $page->update($data);

        return response()->json(['message' => 'Page updated successfully.']);
    }
}
