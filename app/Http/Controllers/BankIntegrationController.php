<?php

namespace App\Http\Controllers;

use App\Models\BankIntegration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BankIntegrationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $integrations = BankIntegration::where('user_id', Auth::id())->get();
        return view('bank-integrations.index', compact('integrations'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $accounts = Auth::user()->accounts;
        return view('bank-integrations.create', compact('accounts'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'account_id' => 'required|exists:accounts,id',
            'bank_name' => 'required|string|max:255',
            'account_number' => 'nullable|string|max:255',
            'account_type' => 'required|in:checking,savings,credit_card',
            'integration_type' => 'required|in:api,csv,ofx,manual',
            'credentials' => 'nullable|array',
            'settings' => 'nullable|array',
            'is_active' => 'boolean',
            'notes' => 'nullable|string',
        ]);

        // Filter out empty credentials
        $credentials = $request->credentials;
        if ($credentials) {
            $credentials = array_filter($credentials, function ($value) {
                return !empty($value) && $value !== '';
            });
            // If all credentials are empty, set to null
            if (empty($credentials)) {
                $credentials = null;
            }
        }

        BankIntegration::create([
            'user_id' => Auth::id(),
            'account_id' => $request->account_id,
            'bank_name' => $request->bank_name,
            'account_number' => $request->account_number,
            'account_type' => $request->account_type,
            'integration_type' => $request->integration_type,
            'credentials' => $credentials,
            'settings' => $request->settings,
            'is_active' => $request->boolean('is_active', true),
            'notes' => $request->notes,
        ]);

        return redirect()->route('bank-integrations.index')->with('success', 'Bank integration created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(BankIntegration $bankIntegration)
    {
        $this->ensureOwner($bankIntegration);
        return view('bank-integrations.show', compact('bankIntegration'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(BankIntegration $bankIntegration)
    {
        $this->ensureOwner($bankIntegration);
        $accounts = Auth::user()->accounts;
        return view('bank-integrations.edit', compact('bankIntegration', 'accounts'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, BankIntegration $bankIntegration)
    {
        $this->ensureOwner($bankIntegration);

        $request->validate([
            'account_id' => 'required|exists:accounts,id',
            'bank_name' => 'required|string|max:255',
            'account_number' => 'nullable|string|max:255',
            'account_type' => 'required|in:checking,savings,credit_card',
            'integration_type' => 'required|in:api,csv,ofx,manual',
            'credentials' => 'nullable|array',
            'settings' => 'nullable|array',
            'is_active' => 'boolean',
            'notes' => 'nullable|string',
        ]);

        // Filter out empty credentials
        $credentials = $request->credentials;
        if ($credentials) {
            $credentials = array_filter($credentials, function ($value) {
                return !empty($value) && $value !== '';
            });
            // If all credentials are empty, set to null
            if (empty($credentials)) {
                $credentials = null;
            }
        }

        $bankIntegration->update([
            'account_id' => $request->account_id,
            'bank_name' => $request->bank_name,
            'account_number' => $request->account_number,
            'account_type' => $request->account_type,
            'integration_type' => $request->integration_type,
            'credentials' => $credentials,
            'settings' => $request->settings,
            'is_active' => $request->boolean('is_active', true),
            'notes' => $request->notes,
        ]);

        return redirect()->route('bank-integrations.index')->with('success', 'Bank integration updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(BankIntegration $bankIntegration)
    {
        $this->ensureOwner($bankIntegration);

        $bankIntegration->delete();

        return redirect()->route('bank-integrations.index')->with('success', 'Bank integration deleted successfully.');
    }

    /**
     * Sync data from bank integration
     */
    public function sync(BankIntegration $bankIntegration)
    {
        $this->ensureOwner($bankIntegration);

        $result = $bankIntegration->syncTransactions();

        if ($result['success']) {
            return response()->json([
                'success' => true,
                'message' => $result['message'],
                'data' => $result
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => $result['message']
            ], 500);
        }
    }

    /**
     * Upload and process OFX file
     */
    public function uploadOfx(Request $request, BankIntegration $bankIntegration)
    {
        $this->ensureOwner($bankIntegration);

        $request->validate([
            'ofx_file' => 'required|file|mimes:ofx,qfx|max:10240', // 10MB max
        ]);

        try {
            $file = $request->file('ofx_file');
            $fileName = 'ofx_' . $bankIntegration->id . '_' . time() . '.' . $file->getClientOriginalExtension();
            $filePath = $file->storeAs('ofx-files', $fileName);

            // Update integration settings with file path
            $settings = $bankIntegration->settings ?? [];
            $settings['ofx_file_path'] = $filePath;
            $bankIntegration->update(['settings' => $settings]);

            // Parse and validate the OFX file
            $ofxContent = file_get_contents(storage_path('app/' . $filePath));
            $parser = app(\App\Services\OfxParser::class);
            $result = $parser->parse($ofxContent);

            return response()->json([
                'success' => true,
                'message' => 'OFX file uploaded successfully',
                'data' => [
                    'file_path' => $filePath,
                    'transactions_found' => count($result['transactions']),
                    'errors' => $result['errors'],
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'OFX upload failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get integration status
     */
    public function status(BankIntegration $bankIntegration)
    {
        $this->ensureOwner($bankIntegration);

        return response()->json($bankIntegration->getStatus());
    }

    private function ensureOwner(BankIntegration $bankIntegration): void
    {
        if ($bankIntegration->user_id !== Auth::id()) {
            abort(403);
        }
    }
}
