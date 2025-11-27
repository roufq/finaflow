<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBankIntegrationRequest;
use App\Http\Requests\UpdateBankIntegrationRequest;
use App\Models\BankIntegration;
use App\Services\EventTracker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BankIntegrationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $integrations = BankIntegration::with('account')
            ->where('user_id', Auth::id())
            ->get();

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
    public function store(StoreBankIntegrationRequest $request, EventTracker $events)
    {
        $validated = $request->validated();

        $credentials = $validated['credentials'] ?? null;
        if ($credentials) {
            $credentials = array_filter($credentials, function ($value) {
                return ! empty($value) && $value !== '';
            });
            if (empty($credentials)) {
                $credentials = null;
            }
        }

        BankIntegration::create([
            'user_id' => Auth::id(),
            'account_id' => $validated['account_id'],
            'bank_name' => $validated['bank_name'],
            'account_number' => $validated['account_number'],
            'account_type' => $validated['account_type'],
            'integration_type' => $validated['integration_type'],
            'credentials' => $credentials,
            'settings' => $validated['settings'] ?? null,
            'is_active' => $request->boolean('is_active', true),
            'notes' => $validated['notes'] ?? null,
        ]);

        $events->log(
            'bank_integration.created',
            [
                'integration_type' => $validated['integration_type'],
                'account_id' => $validated['account_id'],
            ],
            $request,
            Auth::id()
        );

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
    public function update(UpdateBankIntegrationRequest $request, BankIntegration $bankIntegration, EventTracker $events)
    {
        $this->ensureOwner($bankIntegration);

        $validated = $request->validated();

        $credentials = $validated['credentials'] ?? null;
        if ($credentials) {
            $credentials = array_filter($credentials, function ($value) {
                return ! empty($value) && $value !== '';
            });
            if (empty($credentials)) {
                $credentials = null;
            }
        }

        $bankIntegration->update([
            'account_id' => $validated['account_id'],
            'bank_name' => $validated['bank_name'],
            'account_number' => $validated['account_number'],
            'account_type' => $validated['account_type'],
            'integration_type' => $validated['integration_type'],
            'credentials' => $credentials,
            'settings' => $validated['settings'] ?? null,
            'is_active' => $request->boolean('is_active', true),
            'notes' => $validated['notes'] ?? null,
        ]);

        $events->log(
            'bank_integration.updated',
            [
                'integration_type' => $validated['integration_type'],
                'account_id' => $validated['account_id'],
                'integration_id' => $bankIntegration->id,
            ],
            $request,
            Auth::id()
        );

        return redirect()->route('bank-integrations.index')->with('success', 'Bank integration updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(BankIntegration $bankIntegration)
    {
        $this->ensureOwner($bankIntegration);

        app(EventTracker::class)->log(
            'bank_integration.deleted',
            [
                'integration_id' => $bankIntegration->id,
                'integration_type' => $bankIntegration->integration_type,
            ],
            request(),
            Auth::id()
        );

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
                'data' => $result,
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => $result['message'],
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
            $fileName = 'ofx_'.$bankIntegration->id.'_'.time().'.'.$file->getClientOriginalExtension();
            $filePath = $file->storeAs('ofx-files', $fileName);

            // Update integration settings with file path
            $settings = $bankIntegration->settings ?? [];
            $settings['ofx_file_path'] = $filePath;
            $bankIntegration->update(['settings' => $settings]);

            // Parse and validate the OFX file
            $ofxContent = file_get_contents(storage_path('app/'.$filePath));
            $parser = app(\App\Services\OfxParser::class);
            $result = $parser->parse($ofxContent);

            return response()->json([
                'success' => true,
                'message' => 'OFX file uploaded successfully',
                'data' => [
                    'file_path' => $filePath,
                    'transactions_found' => count($result['transactions']),
                    'errors' => $result['errors'],
                ],
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'OFX upload failed: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Upload and store CSV file
     */
    public function uploadCsv(Request $request, BankIntegration $bankIntegration)
    {
        $this->ensureOwner($bankIntegration);

        $request->validate([
            'csv_file' => ['required', 'file', 'mimes:csv,txt', 'max:10240'],
        ]);

        $file = $request->file('csv_file');
        $fileName = 'csv_'.$bankIntegration->id.'_'.time().'.'.$file->getClientOriginalExtension();
        $filePath = $file->storeAs('csv-files', $fileName);

        $settings = $bankIntegration->settings ?? [];
        $settings['csv_file_path'] = $filePath;
        $bankIntegration->update(['settings' => $settings]);

        return response()->json([
            'success' => true,
            'message' => 'CSV file uploaded successfully',
            'data' => [
                'file_path' => $filePath,
            ],
        ]);
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
