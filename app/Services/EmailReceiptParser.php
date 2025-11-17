<?php

namespace App\Services;

class EmailReceiptParser
{
    public function parse(string $content): array
    {
        $content = strip_tags($content);

        preg_match('/(Rp|IDR)?\s?([\d.,]+)/i', $content, $amountMatches);
        $amount = isset($amountMatches[2]) ? (float) str_replace([',', '.'], '', $amountMatches[2]) : 0;

        preg_match('/due\s?(date)?:?\s?(\d{4}-\d{2}-\d{2}|\d{2}[\/-]\d{2}[\/-]\d{4})/i', $content, $dueMatches);
        $dueDate = $dueMatches[2] ?? now()->addDays(7)->format('Y-m-d');
        $dueDate = str_contains($dueDate, '/') ? date('Y-m-d', strtotime(str_replace('/', '-', $dueDate))) : $dueDate;

        preg_match('/from\s?:?\s?(.*)\n/i', $content, $vendorMatches);
        $vendor = isset($vendorMatches[1]) ? trim($vendorMatches[1]) : 'Unknown Vendor';

        preg_match('/invoice\s?(number|#):?\s?([A-Z0-9-]+)/i', $content, $invoiceMatches);
        $invoiceNumber = $invoiceMatches[2] ?? strtoupper(substr(md5($content), 0, 8));

        return [
            'amount' => $amount,
            'due_date' => $dueDate,
            'vendor' => $vendor,
            'invoice_number' => $invoiceNumber,
            'summary' => substr($content, 0, 280),
        ];
    }
}
