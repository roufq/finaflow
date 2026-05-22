<?php

namespace App\Http\Controllers;

use App\Models\EmailSource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EmailSourceController extends Controller
{
    public function index()
    {
        $emailSources = EmailSource::where('user_id', Auth::id())->get();
        return view('email-sources.index', compact('emailSources'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'email_address' => 'required|email|max:255',
        ]);

        // Check if exists for this user
        $exists = EmailSource::where('user_id', Auth::id())
            ->where('email_address', $request->email_address)
            ->exists();

        if ($exists) {
            return back()->with('error', 'Email ini sudah terdaftar.');
        }

        EmailSource::create([
            'user_id' => Auth::id(),
            'email_address' => $request->email_address,
            'is_active' => true,
        ]);

        return back()->with('success', 'Sumber email berhasil ditambahkan.');
    }

    public function destroy(EmailSource $emailSource)
    {
        if ($emailSource->user_id !== Auth::id()) {
            abort(403);
        }

        $emailSource->delete();

        return back()->with('success', 'Sumber email berhasil dihapus.');
    }

    public function toggle(EmailSource $emailSource)
    {
        if ($emailSource->user_id !== Auth::id()) {
            abort(403);
        }

        $emailSource->update([
            'is_active' => !$emailSource->is_active,
        ]);

        return back()->with('success', 'Status sumber email diperbarui.');
    }
}
