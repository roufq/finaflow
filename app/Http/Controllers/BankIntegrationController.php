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
        return view('bank-integrations.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'bank_name' => 'required|string|max:255',
            'account_number' => 'nullable|string|max:255',
            'account_type' => 'required|in:checking,savings,credit_card',
            'integration_type' => 'required|in:api,csv,manual',
            'credentials' => 'nullable|array',
            'settings' => 'nullable|array',
            'is_active' => 'boolean',
            'notes' => 'nullable|string',
        ]);

        BankIntegration::create([
            'user_id' => Auth::id(),
            'bank_name' => $request->bank_name,
            'account_number' => $request->account_number,
            'account_type' => $request->account_type,
            'integration_type' => $request->integration_type,
            'credentials' => $request->credentials,
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
        return view('bank-integrations.edit', compact('bankIntegration'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, BankIntegration $bankIntegration)
    {
        $this->ensureOwner($bankIntegration);

        $request->validate([
            'bank_name' => 'required|string|max:255',
            'account_number' => 'nullable|string|max:255',
            'account_type' => 'required|in:checking,savings,credit_card',
            'integration_type' => 'required|in:api,csv,manual',
            'credentials' => 'nullable|array',
            'settings' => 'nullable|array',
            'is_active' => 'boolean',
            'notes' => 'nullable|string',
        ]);

        $bankIntegration->update($request->only([
            'bank_name', 'account_number', 'account_type', 'integration_type',
            'credentials', 'settings', 'is_active', 'notes'
        ]));

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
     * Sync transactions from bank
     */
    public function sync(BankIntegration $bankIntegration)
    {
        $this->ensureOwner($bankIntegration);

        $result = $bankIntegration->syncTransactions();

        if ($result['success']) {
            return response()->json([
                'success' => true,
                'message' => $result['message'],
                'transactions_imported' => $result['transactions_imported']
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => $result['message']
            ], 500);
        }
    }

    /**
     * Upload CSV file for transaction import
     */
    public function uploadCsv(Request $request, BankIntegration $bankIntegration)
    {
        $this->ensureOwner($bankIntegration);

        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt|max:5120', // 5MB max
        ]);

        try {
            $file = $request->file('csv_file');
            $path = $file->store('temp');

            // Parse CSV and import transactions
            $transactions = $this->parseCsvFile(storage_path('app/' . $path));

            // Check for duplicates
            $duplicates = $bankIntegration->findDuplicateTransactions($transactions);

            // Import transactions (skip duplicates by default)
            $importResult = $bankIntegration->importTransactions($transactions, true);

            // Clean up temp file
            \Storage::delete($path);

            return response()->json([
                'success' => true,
                'imported' => $importResult['imported'],
                'skipped' => $importResult['skipped'],
                'duplicates' => count($duplicates),
                'errors' => count($importResult['errors'])
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'CSV upload failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Parse CSV file and extract transactions
     */
    private function parseCsvFile(string $filePath): array
    {
        $transactions = [];
        $handle = fopen($filePath, 'r');

        // Skip header row
        fgetcsv($handle);

        while (($data = fgetcsv($handle)) !== false) {
            // Assuming CSV format: date,description,amount,type
            if (count($data) >= 4) {
                $transactions[] = [
                    'date' => $data[0],
                    'description' => $data[1],
                    'amount' => (float) str_replace(['Rp', 'IDR', ',', '.'], '', $data[2]),
                    'type' => $data[3] === 'credit' ? 'income' : 'expense',
                ];
            }
        }

        fclose($handle);
        return $transactions;
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
