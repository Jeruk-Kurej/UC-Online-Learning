<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Page;

class PageController extends Controller
{
    public function edit(Page $page)
    {
        return view('admin.pages.edit', compact('page'));
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

    public function uploadImage(Request $request)
    {
        $request->validate([
            'image' => 'required|image|max:5120',
        ]);

        $path = $request->file('image')->store('pages', 'public');

        return response()->json([
            'success' => 1,
            'file' => [
                'url' => asset('storage/' . $path),
            ]
        ]);
    }
}
