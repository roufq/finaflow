<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EducationCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EducationCategoryAdminController extends Controller
{
    public function index(): View
    {
        $categories = EducationCategory::query()->orderBy('order')->paginate(15);

        return view('admin.education-categories.index', compact('categories'));
    }

    public function create(): View
    {
        return view('admin.education-categories.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:255|unique:education_categories,name',
            'description' => 'nullable|string|max:500',
            'order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
        ]);

        EducationCategory::create([
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'order' => $data['order'] ?? 0,
            'is_active' => $data['is_active'] ?? false,
        ]);

        return redirect()->route('admin.education-categories.index')->with('success', 'Category created.');
    }

    public function edit(EducationCategory $educationCategory): View
    {
        return view('admin.education-categories.edit', [
            'category' => $educationCategory,
        ]);
    }

    public function update(Request $request, EducationCategory $educationCategory): RedirectResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:255|unique:education_categories,name,'.$educationCategory->id,
            'description' => 'nullable|string|max:500',
            'order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
        ]);

        $educationCategory->update([
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'order' => $data['order'] ?? 0,
            'is_active' => $data['is_active'] ?? false,
        ]);

        return redirect()->route('admin.education-categories.index')->with('success', 'Category updated.');
    }

    public function destroy(EducationCategory $educationCategory): RedirectResponse
    {
        $educationCategory->delete();

        return back()->with('success', 'Category deleted.');
    }
}
