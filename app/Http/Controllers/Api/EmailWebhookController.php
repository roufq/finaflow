<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\EmailTransactionParserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class EmailWebhookController extends Controller
{
    public function handle(Request $request, EmailTransactionParserService $parser)
    {
        // Log the incoming request to help with debugging setup
        Log::info('Email Webhook Received', $request->except(['body-html', 'html']));

        // Menangani berbagai format provider email (Mailgun, SendGrid, Mailtrap)
        $sender = $request->input('sender') ?? $request->input('from') ?? '';
        $recipient = $request->input('recipient') ?? $request->input('to') ?? '';
        $subject = $request->input('subject') ?? '';
        $body = $request->input('body-plain') ?? $request->input('text') ?? $request->input('body') ?? '';

        // Ekstrak hanya emailnya saja (jika formatnya "Nama <email@domain.com>")
        $senderEmail = $this->extractEmailAddress($sender);
        $recipientEmail = $this->extractEmailAddress($recipient);

        if (!$senderEmail || !$body) {
            Log::warning('Email Webhook failed: Missing sender or body');
            return response()->json(['status' => 'ignored', 'reason' => 'Missing sender or body'], 200); // 200 agar provider tidak retry terus menerus
        }

        // Panggil AI Parser kita
        $transaction = $parser->parseEmailContent($senderEmail, $recipientEmail, $subject, $body);

        if ($transaction) {
            return response()->json([
                'status' => 'success',
                'message' => 'Transaction created successfully',
                'transaction_id' => $transaction->id
            ], 200);
        }

        return response()->json([
            'status' => 'ignored', 
            'reason' => 'Unregistered source or unable to parse'
        ], 200);
    }

    /**
     * Helper untuk mengambil bagian email_address@domain.com dari string.
     */
    protected function extractEmailAddress(string $text): string
    {
        if (preg_match('/<([^>]+)>/', $text, $matches)) {
            return trim($matches[1]);
        }
        return trim($text);
    }
}
