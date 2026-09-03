<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Automation;
use App\Models\Budget;
use App\Models\Category;
use App\Models\Debt;
use App\Models\EducationModule;
use App\Models\FinancialNews;
use App\Models\Goal;
use App\Models\Investment;
use App\Models\Transaction;
use App\Models\Transfer;
use App\Models\User;
use App\Services\Finance\TransactionService;
use App\Services\TransactionCategorizer;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class TelegramController extends Controller
{
    protected $categorizer;

    protected $transactionService;

    public function __construct(TransactionCategorizer $categorizer, TransactionService $transactionService)
    {
        $this->categorizer = $categorizer;
        $this->transactionService = $transactionService;
    }

    public function webhook(Request $request, ?string $token = null)
    {
        if ($request->isMethod('get')) {
            return response()->json([
                'status' => 'online',
                'message' => 'FinaFlow Telegram Webhook is active and waiting for updates.',
                'token_valid' => $token ? User::where('telegram_webhook_token', $token)->exists() : true,
            ]);
        }

        $payload = $request->all();
        $user = null;
        $botToken = null;

        if ($token) {
            $user = User::where('telegram_webhook_token', $token)->first();
            $botToken = $user?->telegram_bot_token;
        }

        $this->processUpdate($payload, $user, $botToken);

        return response()->json(['status' => 'success']);
    }

    public function processUpdate(array $update, ?User $botOwner = null, ?string $botToken = null)
    {
        try {
            // Handle Callback Queries (Inline Buttons)
            if (isset($update['callback_query'])) {
                return $this->handleCallbackQuery($update['callback_query'], $botOwner, $botToken);
            }

            if (! isset($update['message'])) {
                return;
            }

            $message = $update['message'];
            $chatId = $message['chat']['id'];
            $text = $message['text'] ?? '';
            $fromId = $message['from']['id'];

            // 1. Identify User
            $user = $botOwner ?? User::where('telegram_id', $fromId)->first();

            // Auto-link telegram_id for custom bots if not already set
            if ($botOwner && ! $botOwner->telegram_id) {
                $isIdTaken = User::where('telegram_id', $fromId)->where('id', '!=', $botOwner->id)->exists();
                if (! $isIdTaken) {
                    $botOwner->update(['telegram_id' => $fromId]);
                    $user = $botOwner;
                }
            }

            // 2. Handle Commands (Public)
            if (Str::startsWith($text, '/unlink')) {
                $linkedUser = User::where('telegram_id', $fromId)->first();
                if ($linkedUser) {
                    $linkedUser->update([
                        'telegram_id' => null,
                        'telegram_chat_id' => null,
                    ]);
                    $linkedUser->logActivity('Telegram Bot Disconnected', 'Account unlinked via Telegram command /unlink.');
                }

                return $this->sendMessage($chatId, "🔓 *Koneksi Berhasil Diputus*\n\nAkun Telegram Anda sudah tidak terhubung ke FinaFlow. Anda sekarang bisa menghubungkannya ke akun baru menggunakan perintah `/start [token]`.", $botToken);
            }

            if (Str::startsWith($text, '/start')) {
                return $this->handleStart($chatId, $fromId, $text, $botToken);
            }

            if (Str::startsWith($text, '/help')) {
                $helpMsg = "📖 *Panduan Lengkap FinaFlow Bot*\n\n";
                $helpMsg .= "1. *Catat Transaksi*\n";
                $helpMsg .= "   📉 *Keluar:* `[Keterangan] [Nominal]`\n";
                $helpMsg .= "   📈 *Masuk:* `+ [Keterangan] [Nominal]`\n";
                $helpMsg .= "   _Contoh: Nasi padang 25000 BCA_\n\n";

                $helpMsg .= "2. *Transfer Antar Bank*\n";
                $helpMsg .= "   `TF [Asal] ke [Tujuan] [Nominal]`\n";
                $helpMsg .= "   _Contoh: TF BCA ke Jago 500k_\n\n";

                $helpMsg .= "5. *Menu Informasi*\n";
                $helpMsg .= "   • `/accounts` : Lihat semua saldo\n";
                $helpMsg .= "   • `/transactions` : 5 transaksi terakhir\n";
                $helpMsg .= "   • `/summary` : Ringkasan bulan ini\n";
                $helpMsg .= "   • `/categories` : Daftar kategori\n";
                $helpMsg .= "   • `/goals` : Cek target tabungan\n";
                $helpMsg .= "   • `/budgets` : Cek sisa anggaran\n";
                $helpMsg .= "   • `/debts` : Daftar hutang & cicilan\n";
                $helpMsg .= "   • `/reminders` : Jadwal tagihan & pengingat\n";
                $helpMsg .= "   • `/investments` : Portofolio investasi\n";
                $helpMsg .= "   • `/news` : Berita & wawasan finansial\n";
                $helpMsg .= "   • `/learn` : Materi edukasi keuangan\n\n";

                $helpMsg .= "6. *Perintah Baru*\n";
                $helpMsg .= "   • `Kategori baru [Nama]`\n";
                $helpMsg .= "   • `Target baru [Nama] [Nominal]`\n";
                $helpMsg .= "   • `/undo` : Hapus transaksi terakhir\n\n";

                $helpMsg .= "7. *Menu Sistem*\n";
                $helpMsg .= "   • `/start` : Cek status\n";
                $helpMsg .= '   • `/unlink` : Putus koneksi';

                return $this->sendMessage($chatId, $helpMsg, $botToken);
            }

            if (! $user) {
                return $this->sendMessage($chatId, "⚠️ *Koneksi Gagal*\n\nAkun Telegram Anda belum terhubung ke FinaFlow. Silakan buka menu 'Bot Telegram' di aplikasi dan klik 'Hubungkan'.", $botToken);
            }

            // Set Auth Context for Global Scopes & Services
            Auth::onceUsingId($user->id);

            // 3. Handle Protected Commands
            if (Str::startsWith($text, '/accounts')) {
                return $this->handleAccounts($user, $chatId, $botToken);
            }

            if (Str::startsWith($text, '/categories')) {
                return $this->handleCategories($user, $chatId, $botToken);
            }

            if (Str::startsWith($text, '/goals')) {
                return $this->handleGoals($user, $chatId, $botToken);
            }

            if (Str::startsWith($text, '/budgets')) {
                return $this->handleBudgets($user, $chatId, $botToken);
            }

            if (Str::startsWith($text, '/investments')) {
                return $this->handleInvestments($user, $chatId, $botToken);
            }

            if (Str::startsWith($text, '/transactions')) {
                return $this->handleTransactions($user, $chatId, $botToken);
            }

            if (Str::startsWith($text, '/summary')) {
                return $this->handleSummary($user, $chatId, $botToken);
            }

            if (Str::startsWith($text, '/debts')) {
                return $this->handleDebts($user, $chatId, $botToken);
            }

            if (Str::startsWith($text, '/undo')) {
                return $this->handleUndo($user, $chatId, $botToken);
            }

            if (Str::startsWith($text, '/reminders')) {
                return $this->handleReminders($user, $chatId, $botToken);
            }

            if (Str::startsWith($text, '/news')) {
                return $this->handleNews($user, $chatId, $botToken);
            }

            if (Str::startsWith($text, '/learn')) {
                return $this->handleEducation($user, $chatId, $botToken);
            }

            // 4. Detection for Specialized keywords
            if (preg_match('/^(tf|transfer|pindah|pindah saldo)/i', $text)) {
                return $this->handleTransfer($user, $chatId, $text, $botToken);
            }

            if (preg_match('/^(kategori baru|tambah kategori)/i', $text)) {
                return $this->handleCreateCategory($user, $chatId, $text, $botToken);
            }

            if (preg_match('/^(target baru|tambah goal|tambah target)/i', $text)) {
                return $this->handleCreateGoal($user, $chatId, $text, $botToken);
            }

            // 5. Parse and Save Transaction (Fallback)
            return $this->handleTransaction($user, $chatId, $text, $botToken);
        } catch (\Exception $e) {
            Log::error('Telegram Bot Error: '.$e->getMessage(), [
                'update' => $update,
                'trace' => $e->getTraceAsString(),
            ]);

            if (isset($chatId)) {
                return $this->sendMessage($chatId, "❌ *Terjadi Kesalahan*\n\nMaaf, sistem mengalami kendala saat memproses pesan Anda. ".$e->getMessage(), $botToken);
            }
        }
    }

    protected function handleTransfer(User $user, $chatId, $text, ?string $botToken = null)
    {
        $date = $this->parseDate($text);
        $cleanText = $this->removeDateFlags($text);

        // Match: TF [DARI] KE [KE] [NOMINAL]
        if (preg_match('/(?:tf|transfer|pindah)\s+(.*?)\s+(?:ke|to)\s+(.*?)\s+(\d+[\d\.\,k]*)/i', $cleanText, $matches)) {
            $fromName = trim($matches[1]);
            $toName = trim($matches[2]);
            $amountText = $matches[3];
        } else {
            return $this->sendMessage($chatId, "❌ *Format Transfer Salah*\n\nGunakan format: `TF [Akun Asal] ke [Akun Tujuan] [Nominal]`\nContoh: `TF BCA ke Mandiri 500000`", $botToken);
        }

        $amount = $this->parseAmount($amountText);
        $fromAccount = $this->findAccount($user, $fromName);
        $toAccount = $this->findAccount($user, $toName);

        if (! $fromAccount || ! $toAccount) {
            $missing = ! $fromAccount ? $fromName : $toName;

            return $this->sendMessage($chatId, "❌ Akun *'{$missing}'* tidak ditemukan. Pastikan nama akun sesuai.", $botToken);
        }

        DB::transaction(function () use ($user, $fromAccount, $toAccount, $amount, $date) {
            $transfer = Transfer::create([
                'user_id' => $user->id,
                'from_account_id' => $fromAccount->id,
                'to_account_id' => $toAccount->id,
                'amount' => $amount,
                'fee' => 0,
                'transfer_date' => $date,
                'description' => 'Transfer via Telegram',
                'reference_number' => 'TEL-'.strtoupper(Str::random(8)),
                'status' => 'completed',
            ]);

            $transfer->processTransfer();
        });

        $formattedAmount = 'Rp '.number_format($amount, 0, ',', '.');
        $dateStr = $date->format('d M Y');

        return $this->sendMessage($chatId, "✅ *Transfer Berhasil!*\n\n💰 Nominal: *{$formattedAmount}*\n📤 Dari: *{$fromAccount->name}*\n📥 Ke: *{$toAccount->name}*\n📅 Tanggal: *{$dateStr}*", $botToken);
    }

    protected function parseDate(&$text)
    {
        $date = now();

        if (preg_match('/\bkemarin\b/i', $text)) {
            $date = now()->subDay();
        } elseif (preg_match('/\blusa\b/i', $text)) {
            $date = now()->subDays(2);
        }

        if (preg_match('/\b(\d{1,2})[\/\-](\d{1,2})\b/', $text, $matches)) {
            try {
                $day = $matches[1];
                $month = $matches[2];
                $date = Carbon::create(now()->year, $month, $day);
                if ($date->isFuture()) {
                    $date->subYear();
                }
            } catch (\Exception $e) {
            }
        }

        return $date;
    }

    protected function removeDateFlags($text)
    {
        $flags = ['/kemarin/i', '/lusa/i', '/\b\d{1,2}[\/\-]\d{1,2}\b/'];

        return trim(preg_replace($flags, '', $text));
    }

    protected function parseAmount(string $amountText): float
    {
        $amountText = str_ireplace('k', '000', $amountText);
        $amountText = str_replace(['.', ','], '', $amountText);

        return (float) preg_replace('/[^\d]/', '', $amountText);
    }

    protected function findAccount(User $user, ?string $name)
    {
        if (empty($name)) {
            return Account::where('user_id', $user->id)->where('is_active', true)->first();
        }

        return Account::where('user_id', $user->id)
            ->where('is_active', true)
            ->where(function ($query) use ($name) {
                $query->where('name', 'like', "%{$name}%")
                    ->orWhere('bank_name', 'like', "%{$name}%");
            })
            ->first();
    }

    protected function handleAccounts($user, $chatId, ?string $botToken = null)
    {
        $accounts = Account::where('user_id', $user->id)->get();

        if ($accounts->isEmpty()) {
            return $this->sendMessage($chatId, "📭 *Belum Ada Akun*\n\nAnda belum memiliki akun terdaftar di FinaFlow.", $botToken);
        }

        $message = "💰 *Saldo Akun FinaFlow Anda*\n\n";
        foreach ($accounts as $account) {
            $currency = $account->setting->currency_symbol ?? 'Rp';
            $isActive = $account->is_active ? '✅ ' : '🔹 ';
            $bankTag = $account->bank_name ? " [{$account->bank_name}]" : '';

            $message .= "{$isActive}*{$account->name}*{$bankTag}\n   Saldo: {$currency} ".number_format($account->balance, 0, ',', '.')."\n\n";
        }

        $message .= '💡 _Tips: Cukup ketik nama bank saja (misal: BCA) untuk memilih sumber dana._';

        return $this->sendMessage($chatId, $message, $botToken);
    }

    protected function handleStart($chatId, $fromId, $text, ?string $botToken = null)
    {
        $parts = explode(' ', $text);
        if (count($parts) < 2) {
            $user = User::where('telegram_id', $fromId)->first();
            if ($user) {
                return $this->sendMessage($chatId, '👋 *Halo '.($user->name ?? 'User')."!*\n\nAkun Anda sudah terhubung. Berikut cara mencatat transaksi:\n\n📉 *PENGELUARAN:* [Deskripsi] [Nominal]\nContoh: `Beli Bakso 25000`\n\n📈 *PEMASUKAN:* + [Deskripsi] [Nominal]\nContoh: `+ Gaji Bonus 5000000`\n\nKirim `/help` untuk panduan lain.", $botToken);
            }

            return $this->sendMessage($chatId, "👋 *Selamat datang di FinaFlow Bot!*\n\nSilakan gunakan tombol 'Hubungkan Telegram' di dashboard aplikasi untuk menyambungkan akun Anda.", $botToken);
        }

        $token = $parts[1];
        $user = User::where('remember_token', $token)->first();

        if (! $user) {
            return $this->sendMessage($chatId, '❌ Token tidak valid atau sudah kedaluwarsa.', $botToken);
        }

        $isIdTaken = User::where('telegram_id', $fromId)->where('id', '!=', $user->id)->exists();
        if ($isIdTaken) {
            $user->logActivity('Telegram Bot Connection Conflict', 'Failed to connect: Telegram ID is already linked to another FinaFlow account.');

            return $this->sendMessage($chatId, '⚠️ ID Telegram Anda sudah terhubung ke akun FinaFlow lain. Silakan lepaskan koneksi lama terlebih dahulu.', $botToken);
        }

        $user->update([
            'telegram_id' => $fromId,
            'telegram_chat_id' => $chatId,
        ]);

        $user->logActivity('Telegram Bot Connected', 'Account linked via Telegram bot.');

        return $this->sendMessage($chatId, "✅ Halo {$user->name}! Akun Anda berhasil terhubung. Sekarang Anda bisa mencatat transaksi langsung di sini.\n\nContoh: 'Makan siang 25000'", $botToken);
    }

    protected function handleTransaction(User $user, $chatId, $text, ?string $botToken = null)
    {
        // 1. Extract and Remove Date first
        $date = $this->parseDate($text);
        $cleanText = $this->removeDateFlags($text);

        // 2. Identify Type (Income/Expense)
        $isIncome = Str::contains(trim($cleanText), '+');
        $cleanText = str_replace('+', '', trim($cleanText));

        // 3. Match Pattern: [Description] [Amount] [Account Alias]
        // Regex looks for words, then a number, then an optional final word (account)
        if (preg_match('/^(.*?)\s+(\d+[\d\.\,k]*)(?:\s+(.*))?$/i', $cleanText, $matches)) {
            $description = trim($matches[1]);
            $amountText = $matches[2];
            $accountAlias = isset($matches[3]) ? trim($matches[3]) : null;

            $amount = (float) $this->parseAmount($amountText);

            // 4. Determine Account
            $account = $this->findAccount($user, $accountAlias);

            if (! $account) {
                return $this->sendMessage($chatId, '❌ Akun tidak ditemukan. Gunakan nama bank yang terdaftar.', $botToken);
            }

            // 5. Create Transaction
            $guessedCategoryId = $this->categorizer->guessCategoryId($description, $amount, [], $user->id);
            $defaultCategory = Category::where('user_id', $user->id)->where('type', $isIncome ? 'income' : 'expense')->first();

            $transaction = $this->transactionService->create([
                'user_id' => $user->id,
                'account_id' => $account->id,
                'category_id' => $guessedCategoryId ?? $defaultCategory?->id,
                'transaction_date' => $date,
                'type' => $isIncome ? 'income' : 'expense',
                'amount' => $amount,
                'description' => $description ?: ($isIncome ? 'Pemasukan' : 'Pengeluaran'),
            ]);

            $account->refresh();
            $currency = $account->setting->currency_symbol ?? 'Rp';
            $dateStr = $date->isToday() ? 'Hari ini' : $date->format('d M Y');

            $response = "✅ *Berhasil Dicatat!*\n\n";
            $response .= ($isIncome ? '📈' : '📉').' *Type:* '.($isIncome ? 'Pemasukan' : 'Pengeluaran')."\n";
            $response .= "📝 *Item:* {$description}\n";
            $response .= "💵 *Nominal:* {$currency} ".number_format($amount, 0, ',', '.')."\n";
            $response .= "🏦 *Akun:* {$account->name}\n";
            $response .= "📅 *Waktu:* {$dateStr}\n\n";

            if ($guessedCategoryId) {
                $category = Category::find($guessedCategoryId);
                $categoryName = $category ? $category->name : 'Kategori Dihapus';
                $response .= "📂 *Kategori:* {$categoryName}\n\n";
            } else {
                $response .= "⚠️ *Kategori:* Otomatis ({$defaultCategory?->name})\n\n";
            }

            $response .= "💰 *Sisa Saldo:* *{$currency} ".number_format($account->balance, 0, ',', '.').'*';

            // If no certain category, offer options
            $keyboard = null;
            if (! $guessedCategoryId) {
                $categories = Category::where('user_id', $user->id)
                    ->where('type', $isIncome ? 'income' : 'expense')
                    ->latest('id')
                    ->limit(10)
                    ->get();

                if ($categories->isNotEmpty()) {
                    $buttons = [];
                    $row = [];
                    foreach ($categories as $cat) {
                        $row[] = ['text' => '📁 '.$cat->name, 'callback_data' => "set_cat_{$transaction->id}_{$cat->id}"];
                        if (count($row) === 2) {
                            $buttons[] = $row;
                            $row = [];
                        }
                    }
                    if (! empty($row)) {
                        $buttons[] = $row;
                    }
                    $keyboard = ['inline_keyboard' => $buttons];
                    $response .= "\n\n💡 *Pilih kategori yang sesuai:*";
                }
            }

            return $this->sendMessage($chatId, $response, $botToken, $keyboard);
        }

        return $this->sendMessage($chatId, "❓ *Format Belum Pas*\n\n_Contoh:_ `Makan 25000 BCA 01/04`", $botToken);
    }

    protected function handleCategories(User $user, $chatId, ?string $botToken = null)
    {
        $categories = Category::where('user_id', $user->id)->get();
        if ($categories->isEmpty()) {
            return $this->sendMessage($chatId, "📂 *Kategori Kosong*\n\nAnda belum memiliki kategori khusus.", $botToken);
        }

        $message = "📂 *Daftar Kategori Anda*\n\n";
        $income = $categories->where('type', 'income')->pluck('name')->implode(', ');
        $expense = $categories->where('type', 'expense')->pluck('name')->implode(', ');

        $message .= '📈 *Pemasukan:* '.($income ?: '-')."\n\n";
        $message .= '📉 *Pengeluaran:* '.($expense ?: '-')."\n\n";
        $message .= "💡 _Tips: Ketik 'Kategori baru [Nama]' untuk menambah kategori pengeluaran._";

        return $this->sendMessage($chatId, $message, $botToken);
    }

    protected function handleCreateCategory(User $user, $chatId, $text, ?string $botToken = null)
    {
        if (preg_match('/(?:kategori baru|tambah kategori)\s+(.*)/i', $text, $matches)) {
            $name = trim($matches[1]);

            $category = Category::create([
                'user_id' => $user->id,
                'name' => $name,
                'type' => 'expense',
                'color' => '#'.str_pad(dechex(mt_rand(0, 0xFFFFFF)), 6, '0', STR_PAD_LEFT),
            ]);

            return $this->sendMessage($chatId, "✅ *Kategori Berhasil Dibuat!*\n\nNama: *{$category->name}*\nTipe: *Pengeluaran*", $botToken);
        }

        return $this->sendMessage($chatId, '❌ Gagal membuat kategori. Format: `Kategori baru [Nama]`', $botToken);
    }

    protected function handleGoals(User $user, $chatId, ?string $botToken = null)
    {
        $goals = Goal::where('user_id', $user->id)->where('status', 'active')->get();
        if ($goals->isEmpty()) {
            return $this->sendMessage($chatId, "🎯 *Belum Ada Target Aktif*\n\nAnda belum memiliki tujuan keuangan yang sedang berjalan.", $botToken);
        }

        $message = "🎯 *Target Keuangan Anda*\n\n";
        foreach ($goals as $goal) {
            $percent = $goal->progress_percentage;
            $current = 'Rp '.number_format($goal->current_amount, 0, ',', '.');
            $target = 'Rp '.number_format($goal->target_amount, 0, ',', '.');
            $message .= "• *{$goal->name}*\n  Progress: {$percent}% ({$current} / {$target})\n\n";
        }

        return $this->sendMessage($chatId, $message, $botToken);
    }

    protected function handleCreateGoal(User $user, $chatId, $text, ?string $botToken = null)
    {
        if (preg_match('/(?:target baru|tambah goal|tambah target)\s+(.*?)\s+(\d+[\d\.\,k]*)/i', $text, $matches)) {
            $name = trim($matches[1]);
            $amount = $this->parseAmount($matches[2]);

            $goal = Goal::create([
                'user_id' => $user->id,
                'name' => $name,
                'target_amount' => $amount,
                'current_amount' => 0,
                'target_date' => now()->addMonths(6), // Default 6 months
                'category' => 'savings',
                'status' => 'active',
            ]);

            $targetFormatted = 'Rp '.number_format($amount, 0, ',', '.');

            return $this->sendMessage($chatId, "✅ *Target Berhasil Dibuat!*\n\n🎯 Target: *{$goal->name}*\n💰 Nominal: *{$targetFormatted}*\n📅 Deadline: 6 bulan kedepan (Default)", $botToken);
        }

        return $this->sendMessage($chatId, '❌ Gagal membuat target. Format: `Target baru [Nama] [Nominal]`', $botToken);
    }

    protected function handleBudgets(User $user, $chatId, ?string $botToken = null)
    {
        $budgets = Budget::where('user_id', $user->id)->where('status', 'active')->get();
        if ($budgets->isEmpty()) {
            return $this->sendMessage($chatId, "📅 *Belum Ada Anggaran*\n\nAnda belum menyetel budget pengeluaran bulan ini.", $botToken);
        }

        $message = "📅 *Status Anggaran Bulan Ini*\n\n";
        foreach ($budgets as $budget) {
            $remaining = 'Rp '.number_format($budget->remaining_amount, 0, ',', '.');
            $message .= "• *{$budget->type_label}*\n  Sisa: *{$remaining}* ({$budget->spent_percentage}% terpakai)\n\n";
        }

        return $this->sendMessage($chatId, $message, $botToken);
    }

    protected function handleInvestments(User $user, $chatId, ?string $botToken = null)
    {
        $investments = Investment::where('user_id', $user->id)->get();
        if ($investments->isEmpty()) {
            return $this->sendMessage($chatId, "📈 *Portofolio Kosong*\n\nAnda belum mencatat investasi apapun.", $botToken);
        }

        $totalValue = $investments->sum(fn ($i) => $i->current_value);
        $totalGain = $investments->sum(fn ($i) => $i->unrealized_gain_loss);

        $message = "📈 *Portofolio Investasi Anda*\n\n";
        foreach ($investments as $inv) {
            $val = 'Rp '.number_format($inv->current_value, 0, ',', '.');
            $gain = ($inv->unrealized_gain_loss >= 0 ? '+' : '').'Rp '.number_format($inv->unrealized_gain_loss, 0, ',', '.');
            $message .= "• *{$inv->name}* ({$inv->type})\n  Value: {$val}\n  G/L: {$gain}\n\n";
        }

        $totalFormatted = 'Rp '.number_format($totalValue, 0, ',', '.');
        $message .= "💰 *Total Nilai:* *{$totalFormatted}*";

        return $this->sendMessage($chatId, $message, $botToken);
    }

    protected function handleTransactions(User $user, $chatId, ?string $botToken = null)
    {
        $transactions = Transaction::latest('transaction_date')
            ->latest('id')
            ->limit(5)
            ->get();

        if ($transactions->isEmpty()) {
            return $this->sendMessage($chatId, "📝 *Belum Ada Transaksi*\n\nAnda belum mencatat transaksi di FinaFlow.", $botToken);
        }

        $message = "📝 *5 Transaksi Terakhir Anda*\n\n";
        foreach ($transactions as $tx) {
            $icon = $tx->type === 'income' ? '📈' : '📉';
            $date = $tx->transaction_date->format('d/m');
            $currency = $tx->account->setting->currency_symbol ?? 'Rp';
            $amount = number_format($tx->amount, 0, ',', '.');
            $category = $tx->category->name ?? 'Tanpa Kategori';
            $message .= "{$icon} [{$date}] *{$tx->description}*\n   {$category} • {$currency} {$amount}\n\n";
        }

        return $this->sendMessage($chatId, $message, $botToken);
    }

    protected function handleSummary(User $user, $chatId, ?string $botToken = null)
    {
        $startOfMonth = now()->startOfMonth();
        $endOfMonth = now()->endOfMonth();

        $income = Transaction::where('type', 'income')
            ->whereBetween('transaction_date', [$startOfMonth, $endOfMonth])
            ->sum('amount');

        $expense = Transaction::where('type', 'expense')
            ->whereBetween('transaction_date', [$startOfMonth, $endOfMonth])
            ->sum('amount');

        $net = $income - $expense;
        $monthName = now()->format('F Y');

        $message = "📊 *Ringkasan Keuangan ({$monthName})*\n\n";
        $message .= '📈 Pemasukan: *Rp '.number_format($income, 0, ',', '.')."*\n";
        $message .= '📉 Pengeluaran: *Rp '.number_format($expense, 0, ',', '.')."*\n";
        $message .= "──────────────────\n";
        $message .= ($net >= 0 ? '💰' : '⚠️').' Selisih: *Rp '.number_format($net, 0, ',', '.')."*\n\n";

        $keyboard = [
            'inline_keyboard' => [
                [
                    ['text' => '⏮️ Bulan Lalu', 'callback_data' => 'summary_prev_'.now()->subMonth()->format('Y-m')],
                    ['text' => '⏭️ Bulan Depan', 'callback_data' => 'summary_next_'.now()->addMonth()->format('Y-m')],
                ],
                [
                    ['text' => '📊 Detail Kategori', 'callback_data' => 'summary_categories_'.now()->format('Y-m')],
                ],
            ],
        ];

        return $this->sendMessage($chatId, $message, $botToken, $keyboard);
    }

    protected function handleDebts(User $user, $chatId, ?string $botToken = null)
    {
        $debts = Debt::where('user_id', $user->id)->where('current_balance', '>', 0)->get();
        if ($debts->isEmpty()) {
            return $this->sendMessage($chatId, "🎉 *Bebas Hutang!*\n\nAnda tidak memiliki hutang atau cicilan aktif yang tercatat.", $botToken);
        }

        $message = "💳 *Daftar Hutang & Cicilan*\n\n";
        foreach ($debts as $debt) {
            $current = 'Rp '.number_format($debt->current_balance, 0, ',', '.');
            $progress = round($debt->payoff_progress, 1);
            $message .= "• *{$debt->name}* ({$debt->type_label})\n  Sisa: *{$current}* (Progres: {$progress}%)\n\n";
        }

        $total = 'Rp '.number_format($debts->sum('current_balance'), 0, ',', '.');
        $message .= "💸 *Total Kewajiban:* *{$total}*";

        return $this->sendMessage($chatId, $message, $botToken);
    }

    protected function handleUndo(User $user, $chatId, ?string $botToken = null)
    {
        $lastTx = Transaction::where('user_id', $user->id)
            ->where('created_at', '>=', now()->subMinutes(15)) // Only allow undo within 15 minutes
            ->latest()
            ->first();

        if (! $lastTx) {
            return $this->sendMessage($chatId, '❌ *Gagal Undo*\n\nTidak ada transaksi terbaru (dalam 15 menit terakhir) yang bisa dibatalkan.', $botToken);
        }

        $desc = $lastTx->description;
        $amount = number_format($lastTx->amount, 0, ',', '.');
        $this->transactionService->delete($lastTx);

        return $this->sendMessage($chatId, "🗑️ *Transaksi Dibatalkan*\n\nBerhasil menghapus: *{$desc}* senilai *Rp {$amount}*.", $botToken);
    }

    protected function handleReminders(User $user, $chatId, ?string $botToken = null)
    {
        $reminders = Automation::where('user_id', $user->id)
            ->where('type', 'reminder')
            ->where('is_active', true)
            ->get();

        if ($reminders->isEmpty()) {
            return $this->sendMessage($chatId, "🔔 *Belum Ada Pengingat*\n\nAnda tidak memiliki pengingat tagihan aktif.", $botToken);
        }

        $message = "🔔 *Daftar Pengingat Tagihan*\n\n";
        foreach ($reminders as $reminder) {
            $msg = $reminder->actions[0]['params']['message'] ?? $reminder->name;
            $message .= "• {$msg}\n\n";
        }

        return $this->sendMessage($chatId, $message, $botToken);
    }

    protected function handleNews(User $user, $chatId, ?string $botToken = null)
    {
        $news = FinancialNews::recent()->limit(3)->get();
        if ($news->isEmpty()) {
            return $this->sendMessage($chatId, "📰 *Belum Ada Berita*\n\nNantikan wawasan finansial terbaru di sini.", $botToken);
        }

        $message = "📰 *Wawasan Finansial Terbaru*\n\n";
        foreach ($news as $item) {
            $message .= "🔹 *{$item->title}*\n".Str::limit($item->content, 100)."\n[Selengkapnya]({$item->url})\n\n";
        }

        return $this->sendMessage($chatId, $message, $botToken);
    }

    protected function handleEducation(User $user, $chatId, ?string $botToken = null)
    {
        $modules = EducationModule::active()->limit(3)->get();
        if ($modules->isEmpty()) {
            return $this->sendMessage($chatId, "📚 *Materi Belum Tersedia*\n\nNantikan modul edukasi keuangan terbaru di sini.", $botToken);
        }

        $message = "📚 *Edukasi Keuangan FinaFlow*\n\n";
        foreach ($modules as $module) {
            $diff = $module->difficulty_label;
            $time = $module->estimated_time ?? '10';
            $message .= "📘 *{$module->title}* ({$diff})\n⏱️ Estimasi: {$time} menit\n[Mulai Belajar](".url("/education/module/{$module->id}").")\n\n";
        }

        return $this->sendMessage($chatId, $message, $botToken);
    }

    protected function handleCallbackQuery(array $callbackQuery, ?User $botOwner = null, ?string $botToken = null)
    {
        $chatId = $callbackQuery['message']['chat']['id'];
        $messageId = $callbackQuery['message']['message_id'];
        $data = $callbackQuery['data'];
        $fromId = $callbackQuery['from']['id'];

        $user = $botOwner ?? User::where('telegram_id', $fromId)->first();
        if (! $user) {
            return;
        }

        Auth::onceUsingId($user->id);

        if (Str::startsWith($data, 'set_cat_')) {
            // Format: set_cat_[tx_id]_[cat_id]
            if (preg_match('/set_cat_(\d+)_(\d+)/', $data, $matches)) {
                $txId = $matches[1];
                $catId = $matches[2];

                $transaction = Transaction::find($txId);
                $category = Category::find($catId);

                if ($transaction && $category && $transaction->user_id === $user->id) {
                    $transaction->update(['category_id' => $catId]);
                    $this->editMessageText($chatId, $messageId, $callbackQuery['message']['text']."\n\n✅ *Kategori diperbarui menjadi:* {$category->name}", $botToken);
                }
            }
        }

        if (Str::startsWith($data, 'summary_')) {
            // TODO: Implement month navigation if needed, for now just show notice
            $this->sendMessage($chatId, '🚧 Fitur navigasi laporan sedang dikembangkan.', $botToken);
        }

        // Answer callback to remove loading state
        $this->answerCallbackQuery($callbackQuery['id'], $botToken);
    }

    protected function editMessageText($chatId, $messageId, $text, ?string $botToken = null)
    {
        $token = $botToken ?? config('services.telegram.bot_token');
        $baseUrl = config('services.telegram.base_url', 'https://api.telegram.org');
        $url = "{$baseUrl}/bot{$token}/editMessageText";

        Http::withoutVerifying()->post($url, [
            'chat_id' => $chatId,
            'message_id' => $messageId,
            'text' => $text,
            'parse_mode' => 'Markdown',
        ]);
    }

    protected function answerCallbackQuery($callbackQueryId, ?string $botToken = null)
    {
        $token = $botToken ?? config('services.telegram.bot_token');
        $baseUrl = config('services.telegram.base_url', 'https://api.telegram.org');
        $url = "{$baseUrl}/bot{$token}/answerCallbackQuery";

        Http::withoutVerifying()->post($url, [
            'callback_query_id' => $callbackQueryId,
        ]);
    }

    public function sendMessage($chatId, $text, ?string $botToken = null, ?array $replyMarkup = null)
    {
        $token = $botToken ?? config('services.telegram.bot_token');
        if (! $token) {
            return null;
        }

        $baseUrl = config('services.telegram.base_url', 'https://api.telegram.org');
        $url = "{$baseUrl}/bot{$token}/sendMessage";
        try {
            $payload = [
                'chat_id' => $chatId,
                'text' => $text,
                'parse_mode' => 'Markdown',
            ];

            if ($replyMarkup) {
                $payload['reply_markup'] = json_encode($replyMarkup);
            }

            Http::withoutVerifying()->post($url, $payload)->throw();
        } catch (\Exception $e) {
            Log::error('Telegram error: '.$e->getMessage());
        }

        return true;
    }
}
