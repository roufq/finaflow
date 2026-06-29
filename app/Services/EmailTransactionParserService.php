<?php

namespace App\Services;

use App\Models\Account;
use App\Models\Category;
use App\Models\EmailSource;
use App\Models\Transaction;
use Illuminate\Support\Facades\Log;

class EmailTransactionParserService
{
    /**
     * Parses an email and automatically creates a transaction if criteria match.
     */
    public function parseEmailContent(string $senderEmail, string $recipientEmail, string $subject, string $body)
    {
        // 1. Check if recipient or sender is a registered EmailSource
        $source = EmailSource::where(function ($q) use ($senderEmail, $recipientEmail) {
            $q->where('email_address', $senderEmail)
                ->orWhere('email_address', $recipientEmail);
        })->where('is_active', true)->first();

        if (! $source) {
            Log::info("EmailTransactionParserService: Email ignored. Unregistered source. Sender: {$senderEmail}, Recipient: {$recipientEmail}");

            return false;
        }

        // Update the timestamp so the UI shows it's connected and receiving data
        $source->update(['last_received_at' => now()]);

        $userId = $source->user_id;

        // 2. Extract Amount
        $amount = $this->extractAmount($body);
        if (! $amount) {
            Log::info('EmailTransactionParserService: Could not detect amount in email body.');

            return false;
        }

        // 3. Extract Source (Account/Bank)
        $bankName = $this->extractBankName($body, $subject, $userId);
        $account = $this->findOrCreateAccount($userId, $bankName);

        // 4. Detect Type (Income or Expense)
        $type = $this->detectTransactionType($body, $subject);

        // 5. Extract Description
        $description = $this->extractDescription($body, $subject) ?: 'Transaksi Email Otomatis';

        // 6. Find a generic category based on type
        // This is a naive category fallback. In a real scenario, we might use AI to map description to category.
        $category = Category::where('user_id', $userId)
            ->where('type', $type)
            ->first();

        // 7. Create Transaction
        $transaction = Transaction::create([
            'user_id' => $userId,
            'account_id' => $account->id,
            'category_id' => $category ? $category->id : null,
            'type' => $type,
            'amount' => $amount,
            'description' => $description,
            'transaction_date' => now(),
            'notes' => "Dibuat otomatis dari AI Email Parser.\nSubject: {$subject}",
        ]);

        Log::info("EmailTransactionParserService: Successfully created {$type} transaction of {$amount} for user {$userId}.");

        return $transaction;
    }

    protected function extractAmount(string $text): ?float
    {
        // Matches typical Indonesian formatting: Rp 12.000, 12,000.00, IDR 50.000
        if (preg_match('/(?:Rp|IDR)?\s*([0-9]{1,3}(?:[.,][0-9]{3})*(?:[.,][0-9]{1,2})?)/i', $text, $matches)) {
            // Clean up formatting
            $numberStr = str_replace(',', '', $matches[1]);
            // If it uses dots for thousands
            if (substr_count($numberStr, '.') > 1 || (strlen($numberStr) - strrpos($numberStr, '.')) == 4) {
                $numberStr = str_replace('.', '', $numberStr);
            }

            return (float) $numberStr;
        }

        return null;
    }

    protected function extractBankName(string $body, string $subject, int $userId): string
    {
        $userAccounts = Account::where('user_id', $userId)->pluck('name')->toArray();
        $textToSearch = strtoupper($subject.' '.$body);

        // Tahap 1: Cek nama persis (exact match)
        foreach ($userAccounts as $accountName) {
            if (strpos($textToSearch, strtoupper($accountName)) !== false) {
                return $accountName;
            }
        }

        // Tahap 2: Cek kata kunci jika nama akun panjang (misal "Tabungan Mandiri" -> temukan "Mandiri")
        $ignoreWords = ['REKENING', 'TABUNGAN', 'DOMPET', 'BANK', 'AKUN', 'UTAMA', 'KARTU'];
        foreach ($userAccounts as $accountName) {
            $words = explode(' ', strtoupper($accountName));
            foreach ($words as $word) {
                $word = trim($word);
                // Hanya cari kata unik (panjang > 2 dan bukan kata umum)
                if (strlen($word) > 2 && ! in_array($word, $ignoreWords)) {
                    if (strpos($textToSearch, $word) !== false) {
                        return $accountName;
                    }
                }
            }
        }

        return 'Email/Bank Default';
    }

    protected function findOrCreateAccount(int $userId, string $bankName)
    {
        $account = Account::where('user_id', $userId)
            ->where('name', 'like', "%{$bankName}%")
            ->first();

        if (! $account) {
            $account = Account::create([
                'user_id' => $userId,
                'name' => $bankName,
                'type' => 'bank',
                'balance' => 0,
            ]);
        }

        return $account;
    }

    protected function detectTransactionType(string $body, string $subject): string
    {
        $text = strtolower($subject.' '.$body);

        $expenseKeywords = ['keluar', 'debet', 'pembayaran', 'transfer ke', 'pembelian', 'tagihan', 'bayar', 'beli', 'nasi', 'makan', 'gojek', 'grab', 'tokopedia', 'shopee'];
        $incomeKeywords = ['masuk', 'kredit', 'penerimaan', 'transfer dari', 'gaji', 'bonus', 'refund', 'pencairan', 'pemberian'];

        // Usually 'masuk' or 'kredit' is very explicit for income.
        foreach ($incomeKeywords as $kw) {
            if (strpos($text, $kw) !== false) {
                return 'income';
            }
        }

        // Check expense
        foreach ($expenseKeywords as $kw) {
            if (strpos($text, $kw) !== false) {
                return 'expense';
            }
        }

        // Default to expense if unclear
        return 'expense';
    }

    protected function extractDescription(string $body, string $subject): string
    {
        // Try to find what comes after "untuk" or "keterangan"
        if (preg_match('/(?:untuk|keterangan|pesan|berita)\s*:\s*(.*?)(?:\n|\r|\.|$)/i', $body, $matches)) {
            return trim($matches[1]);
        }

        // If it's short, just use the subject
        if (strlen($subject) > 5 && strlen($subject) < 100) {
            return $subject;
        }

        return 'Transaksi otomatis via Email';
    }
}
