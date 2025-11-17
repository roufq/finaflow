<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Category;
use App\Models\SpendingTrigger;
use App\Models\Gamification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use thiagoalessio\TesseractOCR\TesseractOCR;
// use Google\Cloud\Vision\V1\ImageAnnotatorClient;
// use Google\Cloud\Vision\V1\Feature\Type;

class TransactionController extends Controller
{
    public function index()
    {
        $transactions = Transaction::with('category')->orderBy('transaction_date', 'desc')->get();
        $categories = Category::where('user_id', Auth::id())->get();
        return view('transactions.index', compact('transactions', 'categories'));
    }

    public function create()
    {
        $categories = Category::all();
        return view('transactions.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'transaction_date' => 'required|date',
            'type' => 'required|in:income,expense',
            'amount' => 'required|numeric|min:0',
            'description' => 'nullable|string',
        ]);

        $transaction = Transaction::create([
            'user_id' => Auth::id(),
            'category_id' => $request->category_id,
            'transaction_date' => $request->transaction_date,
            'type' => $request->type,
            'amount' => $request->amount,
            'description' => $request->description,
        ]);

        // Check for spending triggers and award gamification points
        if ($request->type === 'expense') {
            $this->checkSpendingTriggers($transaction);
            $this->awardGamificationPoints($transaction);
        }

        return redirect()->route('transactions.index')->with('success', 'Transaction created successfully.');
    }

    public function show(Transaction $transaction)
    {
        return view('transactions.show', compact('transaction'));
    }

    public function edit(Transaction $transaction)
    {
        $categories = Category::all();
        return view('transactions.edit', compact('transaction', 'categories'));
    }

    public function update(Request $request, Transaction $transaction)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'transaction_date' => 'required|date',
            'type' => 'required|in:income,expense',
            'amount' => 'required|numeric|min:0',
            'description' => 'nullable|string',
        ]);

        $transaction->update($request->only(['category_id', 'transaction_date', 'type', 'amount', 'description']));

        return redirect()->route('transactions.index')->with('success', 'Transaction updated successfully.');
    }

    public function destroy(Transaction $transaction)
    {
        $transaction->delete();
        return redirect()->route('transactions.index')->with('success', 'Transaction deleted successfully.');
    }

    public function scanReceipt(Request $request)
    {
        // Skip CSRF validation for this endpoint
        $request->validate([
            'receipt_image' => 'required|image|mimes:jpeg,png,jpg|max:5120', // 5MB max
        ]);

        try {
            $image = $request->file('receipt_image');
            $imagePath = $image->getPathname();

            // Use Tesseract OCR to extract text from the actual uploaded receipt image
            $tesseract = new TesseractOCR($imagePath);
            $tesseract->executable('C:\Program Files\Tesseract-OCR\tesseract.exe');
            $tesseract->tessdataDir('C:\Program Files\Tesseract-OCR\tessdata');
            $tesseract->lang('eng'); // Use English for OCR
            $extractedText = $tesseract->run();

            // If no text was extracted, provide fallback
            if (empty(trim($extractedText))) {
                $extractedText = "No text could be extracted from the image. Please ensure the receipt image is clear and well-lit.";
            }

            // Parse the extracted text to find transaction details
            $extractedData = $this->parseReceiptText($extractedText);

            return response()->json([
                'success' => true,
                'date' => $extractedData['date'],
                'amount' => $extractedData['amount'],
                'merchant' => $extractedData['merchant'],
                'description' => $extractedData['description'],
                'raw_text' => $extractedText, // Include raw extracted text for debugging
            ]);

        } catch (\thiagoalessio\TesseractOCR\TesseractOcrException $e) {
            // Handle Tesseract-specific errors
            return response()->json([
                'success' => false,
                'message' => 'OCR processing failed: ' . $e->getMessage()
            ], 500);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error processing receipt: ' . $e->getMessage()
            ], 500);
        }
    }

    private function parseReceiptText($text)
    {
        $lines = explode("\n", $text);
        $data = [
            'date' => now()->format('Y-m-d'),
            'amount' => null,
            'merchant' => 'Unknown Merchant',
            'description' => 'Receipt transaction',
        ];

        // Extract merchant name from the first few lines of the receipt
        // Typically, the merchant name appears at the top of the receipt
        $potentialMerchantLines = array_slice($lines, 0, 5); // Check first 5 lines

        foreach ($potentialMerchantLines as $line) {
            $line = trim($line);
            if (!empty($line) && strlen($line) > 2 && !is_numeric($line)) {
                // Skip lines that are just numbers, dates, or too short
                if (!preg_match('/^\d/', $line) && !preg_match('/^\d{1,2}[\/\-]\d{1,2}/', $line)) {
                    // Clean up the merchant name
                    $merchant = preg_replace('/[^a-zA-Z\s&\'-]/', '', $line);
                    $merchant = trim($merchant);
                    if (strlen($merchant) > 2) {
                        $data['merchant'] = ucwords(strtolower($merchant));
                        $data['description'] = 'Purchase at ' . $data['merchant'];
                        break;
                    }
                }
            }
        }

        // Look for date patterns (DD/MM/YYYY, DD-MM-YYYY, etc.)
        $datePatterns = [
            '/(\d{1,2})[\/\-](\d{1,2})[\/\-](\d{4})/',
            '/(\d{4})[\/\-](\d{1,2})[\/\-](\d{1,2})/',
            '/(\d{1,2})\s+(jan|feb|mar|apr|may|jun|jul|aug|sep|oct|nov|dec)\s+(\d{4})/i'
        ];

        foreach ($lines as $line) {
            foreach ($datePatterns as $pattern) {
                if (preg_match($pattern, $line, $matches)) {
                    try {
                        if (count($matches) >= 4) {
                            $data['date'] = date('Y-m-d', strtotime($matches[0]));
                        }
                    } catch (\Exception $e) {
                        // Keep default date if parsing fails
                    }
                    break 2;
                }
            }
        }

        // Look for amount patterns (IDR, Rp, USD, $, etc.)
        $amountPatterns = [
            '/(?:rp|idr|rupiah|\$|usd)\s*([\d,]+(?:\.\d{2})?)/i',
            '/total\s*[:\-]?\s*(?:rp|idr|rupiah|\$|usd)?\s*([\d,]+(?:\.\d{2})?)/i',
            '/jumlah\s*[:\-]?\s*(?:rp|idr|rupiah|\$|usd)?\s*([\d,]+(?:\.\d{2})?)/i',
            '/bayar\s*[:\-]?\s*(?:rp|idr|rupiah|\$|usd)?\s*([\d,]+(?:\.\d{2})?)/i',
            '/([\d,]+(?:\.\d{2})?)\s*(?:rp|idr|rupiah|\$|usd)/i'
        ];

        foreach ($lines as $line) {
            foreach ($amountPatterns as $pattern) {
                if (preg_match($pattern, $line, $matches)) {
                    $amount = str_replace(',', '', $matches[1]);
                    if (is_numeric($amount)) {
                        $data['amount'] = (float) $amount;
                        break 2;
                    }
                }
            }
        }

        return $data;
    }

    /**
     * Check for spending triggers based on transaction
     */
    private function checkSpendingTriggers(Transaction $transaction): void
    {
        $triggers = SpendingTrigger::where('user_id', $transaction->user_id)->get();

        foreach ($triggers as $trigger) {
            if ($trigger->matchesTransaction($transaction)) {
                $trigger->incrementFrequency();

                // Log the trigger activation
                \Log::info("Spending trigger activated: {$trigger->description} for transaction ID {$transaction->id}");
            }
        }
    }

    /**
     * Award gamification points for transactions
     */
    private function awardGamificationPoints(Transaction $transaction): void
    {
        $gamification = Gamification::firstOrCreate(
            ['user_id' => $transaction->user_id],
            ['points' => 0, 'level' => 1, 'streak_days' => 0]
        );

        // Award points based on transaction amount (1 point per 1000 spent)
        $pointsEarned = floor($transaction->amount / 1000);
        if ($pointsEarned > 0) {
            $gamification->addPoints($pointsEarned);

            // Award achievement for first transaction of the day
            $todayTransactions = Transaction::where('user_id', $transaction->user_id)
                ->whereDate('transaction_date', today())
                ->count();

            if ($todayTransactions === 1) {
                $gamification->awardAchievement('First Transaction of the Day');
            }
        }
    }
}
