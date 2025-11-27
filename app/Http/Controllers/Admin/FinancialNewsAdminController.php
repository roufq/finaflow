<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FinancialNews;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FinancialNewsAdminController extends Controller
{
    public function index(): View
    {
        $news = FinancialNews::query()->orderByDesc('published_at')->paginate(15);

        return view('admin.news.index', compact('news'));
    }

    public function create(): View
    {
        return view('admin.news.create');
    }

    public function show(FinancialNews $news): View
    {
        return view('admin.news.show', compact('news'));
    }

    public function edit(FinancialNews $news): View
    {
        return view('admin.news.edit', compact('news'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'nullable|string',
            'source' => 'nullable|string|max:255',
            'category' => 'nullable|string|max:255',
            'published_at' => 'nullable|date',
            'url' => 'nullable|url',
        ]);

        FinancialNews::create(array_merge($data, [
            'tags' => $request->input('tags', []),
        ]));

        return back()->with('success', 'News created.');
    }

    public function update(Request $request, FinancialNews $news): RedirectResponse
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'nullable|string',
            'source' => 'nullable|string|max:255',
            'category' => 'nullable|string|max:255',
            'published_at' => 'nullable|date',
            'url' => 'nullable|url',
        ]);

        $news->update(array_merge($data, [
            'tags' => $request->input('tags', []),
        ]));

        return back()->with('success', 'News updated.');
    }

    public function destroy(FinancialNews $news): RedirectResponse
    {
        $news->delete();

        return back()->with('success', 'News deleted.');
    }
}
