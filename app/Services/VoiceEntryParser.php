<?php

namespace App\Services;

class VoiceEntryParser
{
    public function parse(string $text): array
    {
        $text = strtolower(trim($text));

        $type = str_contains($text, 'income') || str_contains($text, 'gaji') ? 'income' : 'expense';

        preg_match('/(\d+[\d.,]*)/', $text, $amountMatches);
        $amount = isset($amountMatches[1]) ? (float) str_replace([',', '.'], '', $amountMatches[1]) : 0;

        preg_match('/on (\d{4}-\d{2}-\d{2})/', $text, $dateMatches);
        $date = $dateMatches[1] ?? now()->format('Y-m-d');

        $description = preg_replace('/(income|expense|spend|bayar|pay|at|on|\d{4}-\d{2}-\d{2}|\d+[\d.,]*)/i', '', $text);
        $description = trim($description) ?: 'Voice entry';

        return [
            'type' => $type,
            'amount' => $amount,
            'date' => $date,
            'description' => ucwords($description),
        ];
    }
}
