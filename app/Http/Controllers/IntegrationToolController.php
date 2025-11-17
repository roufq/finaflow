<?php

namespace App\Http\Controllers;

use App\Models\Automation;
use App\Models\Transaction;
use App\Services\EmailReceiptParser;
use App\Services\TransactionCategorizer;
use App\Services\VoiceEntryParser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IntegrationToolController extends Controller
{
    public function voiceEntry()
    {
        return view('integrations.voice-entry');
    }

    public function storeVoiceEntry(Request $request, VoiceEntryParser $parser, TransactionCategorizer $categorizer)
    {
        $request->validate([
            'voice_text' => 'required|string|min:5',
        ]);

        $parsed = $parser->parse($request->voice_text);

        $transaction = Transaction::create([
            'user_id' => Auth::id(),
            'category_id' => $categorizer->guessCategoryId($parsed['description'], $parsed['amount'], $parsed),
            'transaction_date' => $parsed['date'],
            'type' => $parsed['type'],
            'amount' => $parsed['amount'],
            'description' => $parsed['description'],
            'location_metadata' => ['source' => 'voice-entry'],
        ]);

        return back()->with([
            'success' => 'Voice entry converted to transaction.',
            'parsed' => $parsed,
            'transaction_id' => $transaction->id,
        ]);
    }

    public function emailParser()
    {
        return view('integrations.email-parser');
    }

    public function parseEmail(Request $request, EmailReceiptParser $parser)
    {
        $request->validate([
            'email_content' => 'required|string|min:20',
        ]);

        $parsed = $parser->parse($request->email_content);

        return back()->with([
            'success' => 'Email parsed successfully.',
            'parsed' => $parsed,
        ]);
    }

    public function reminders()
    {
        $reminders = Automation::where('user_id', Auth::id())
            ->where('type', 'reminder')
            ->get();

        return view('integrations.bill-reminders', compact('reminders'));
    }

    public function storeReminder(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'due_date' => 'required|date',
            'vendor' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'reminder_days' => 'required|integer|min:1|max:30',
        ]);

        Automation::create([
            'user_id' => Auth::id(),
            'name' => $request->name,
            'description' => "Bill reminder for {$request->vendor}",
            'type' => 'reminder',
            'conditions' => [
                [
                    'field' => 'due_date',
                    'operator' => 'equals',
                    'value' => $request->due_date,
                ],
            ],
            'actions' => [
                [
                    'type' => 'send_notification',
                    'params' => [
                        'message' => "Bill {$request->vendor} (Rp {$request->amount}) due on {$request->due_date}",
                        'reminder_days' => $request->reminder_days,
                    ],
                ],
            ],
            'is_active' => true,
        ]);

        return redirect()->route('integrations.reminders')->with('success', 'Reminder created successfully.');
    }
}
