<?php

namespace App\Http\Controllers;

use App\Models\TaxDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class TaxDocumentController extends Controller
{
    public function index()
    {
        $documents = TaxDocument::orderBy('year', 'desc')->orderBy('created_at', 'desc')->get();
        return view('tax-documents.index', compact('documents'));
    }

    public function create()
    {
        return view('tax-documents.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'year' => 'required|integer|min:2000|max:' . (now()->year + 1),
            'category' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
            'document' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        $path = $request->file('document')->store('tax-documents', 'public');

        TaxDocument::create([
            'user_id' => Auth::id(),
            'title' => $request->title,
            'year' => $request->year,
            'category' => $request->category,
            'file_path' => $path,
            'notes' => $request->notes,
        ]);

        return redirect()->route('tax-documents.index')->with('success', 'Tax document uploaded successfully.');
    }

    public function show(TaxDocument $tax_document)
    {
        return view('tax-documents.show', ['document' => $tax_document]);
    }

    public function edit(TaxDocument $tax_document)
    {
        return view('tax-documents.edit', ['document' => $tax_document]);
    }

    public function update(Request $request, TaxDocument $tax_document)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'year' => 'required|integer|min:2000|max:' . (now()->year + 1),
            'category' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
            'document' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        $data = $request->only(['title', 'year', 'category', 'notes']);

        if ($request->hasFile('document')) {
            if ($tax_document->file_path && Storage::disk('public')->exists($tax_document->file_path)) {
                Storage::disk('public')->delete($tax_document->file_path);
            }

            $data['file_path'] = $request->file('document')->store('tax-documents', 'public');
        }

        $tax_document->update($data);

        return redirect()->route('tax-documents.index')->with('success', 'Tax document updated successfully.');
    }

    public function destroy(TaxDocument $tax_document)
    {
        if ($tax_document->file_path && Storage::disk('public')->exists($tax_document->file_path)) {
            Storage::disk('public')->delete($tax_document->file_path);
        }

        $tax_document->delete();

        return redirect()->route('tax-documents.index')->with('success', 'Tax document deleted successfully.');
    }
}

