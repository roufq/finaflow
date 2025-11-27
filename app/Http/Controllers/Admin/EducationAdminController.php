<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EducationCategory;
use App\Models\EducationModule;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EducationAdminController extends Controller
{
    public function index(): View
    {
        $modules = EducationModule::query()->orderBy('order')->paginate(15);

        return view('admin.education.index', compact('modules'));
    }

    public function show(EducationModule $module): View
    {
        return view('admin.education.show', compact('module'));
    }

    public function create(): View
    {
        $categories = EducationCategory::query()->orderBy('order')->get();

        return view('admin.education.create', compact('categories'));
    }

    public function edit(EducationModule $module): View
    {
        $categories = EducationCategory::query()->orderBy('order')->get();

        return view('admin.education.edit', compact('module', 'categories'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'nullable|string|max:255',
            'difficulty' => 'nullable|string|in:beginner,intermediate,advanced',
            'estimated_time' => 'nullable|integer|min:0',
            'order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
        ]);

        $module = EducationModule::create([
            'title' => $data['title'],
            'content' => $request->input('content'),
            'category' => $data['category'] ?? null,
            'difficulty' => $data['difficulty'] ?? 'beginner',
            'estimated_time' => $data['estimated_time'] ?? 0,
            'order' => $data['order'] ?? 0,
            'is_active' => $data['is_active'] ?? false,
            'tags' => $request->input('tags', []),
            'learning_objectives' => $request->input('learning_objectives', []),
            'resource_links' => $request->input('resource_links', []),
            'language' => $request->input('language', 'id'),
        ]);

        $message = 'Module created.';

        if ($request->wantsJson()) {
            return response()->json([
                'message' => $message,
                'redirect' => route('admin.education.index'),
                'module_id' => $module->id,
            ], 201);
        }

        return redirect()->route('admin.education.index')->with('success', $message);
    }

    public function update(Request $request, EducationModule $module): RedirectResponse
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'nullable|string|max:255',
            'difficulty' => 'nullable|string|in:beginner,intermediate,advanced',
            'estimated_time' => 'nullable|integer|min:0',
            'order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
        ]);

        $module->update([
            'title' => $data['title'],
            'content' => $request->input('content'),
            'category' => $data['category'] ?? null,
            'difficulty' => $data['difficulty'] ?? 'beginner',
            'estimated_time' => $data['estimated_time'] ?? 0,
            'order' => $data['order'] ?? 0,
            'is_active' => $data['is_active'] ?? false,
            'tags' => $request->input('tags', []),
            'learning_objectives' => $request->input('learning_objectives', []),
            'resource_links' => $request->input('resource_links', []),
            'language' => $request->input('language', 'id'),
        ]);

        $message = 'Module updated.';

        if ($request->wantsJson()) {
            return response()->json([
                'message' => $message,
                'redirect' => route('admin.education.index'),
                'module_id' => $module->id,
            ]);
        }

        return redirect()->route('admin.education.index')->with('success', $message);
    }

    public function destroy(EducationModule $module): RedirectResponse
    {
        $module->delete();

        return back()->with('success', 'Module deleted.');
    }
}
