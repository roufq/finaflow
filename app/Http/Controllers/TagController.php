<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class TagController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $tags = Tag::where('user_id', Auth::id())
            ->orderBy('name')
            ->get();

        return view('tags.index', compact('tags'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('tags.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('tags')->where(function ($query) {
                    return $query->where('user_id', Auth::id());
                }),
            ],
            'color' => 'nullable|string|regex:/^#[a-fA-F0-9]{6}$/',
            'description' => 'nullable|string|max:1000',
        ]);

        Tag::create([
            'user_id' => Auth::id(),
            'name' => $request->name,
            'color' => $request->color ?: '#007bff',
            'description' => $request->description,
        ]);

        return redirect()->route('tags.index')
            ->with('success', 'Tag berhasil dibuat.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Tag $tag)
    {
        $this->authorize('view', $tag);

        $transactions = $tag->transactions()
            ->with(['category', 'account'])
            ->latest('transaction_date')
            ->paginate(15);

        return view('tags.show', compact('tag', 'transactions'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Tag $tag)
    {
        $this->authorize('update', $tag);

        return view('tags.edit', compact('tag'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Tag $tag)
    {
        $this->authorize('update', $tag);

        $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('tags')->where(function ($query) use ($tag) {
                    return $query->where('user_id', Auth::id())
                        ->where('id', '!=', $tag->id);
                }),
            ],
            'color' => 'nullable|string|regex:/^#[a-fA-F0-9]{6}$/',
            'description' => 'nullable|string|max:1000',
        ]);

        $tag->update([
            'name' => $request->name,
            'color' => $request->color ?: '#007bff',
            'description' => $request->description,
        ]);

        return redirect()->route('tags.index')
            ->with('success', 'Tag berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Tag $tag)
    {
        $this->authorize('delete', $tag);

        // Check if tag is being used by transactions
        if ($tag->transactions()->count() > 0) {
            return redirect()->route('tags.index')
                ->with('error', 'Tag tidak dapat dihapus karena masih digunakan oleh transaksi.');
        }

        $tag->delete();

        return redirect()->route('tags.index')
            ->with('success', 'Tag berhasil dihapus.');
    }
}
