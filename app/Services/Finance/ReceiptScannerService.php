<?php

namespace App\Services\Finance;

use Carbon\Carbon;
use Illuminate\Support\Str;
use thiagoalessio\TesseractOCR\TesseractOCR;

class ReceiptScannerService
{
    public function extract(string $filePath): array
    {
        $tesseract = new TesseractOCR($filePath);

        $executable = config('services.tesseract.path');
        if (! empty($executable)) {
            $tesseract->executable($executable);
        }

        $tessdata = config('services.tesseract.tessdata');
        if (! empty($tessdata)) {
            $tesseract->tessdataDir($tessdata);
        }

        $tesseract->lang('eng');

        $rawText = $tesseract->run();
        $rawText = trim($rawText);

        if ($rawText === '') {
            $rawText = 'No text could be extracted from the image. Please ensure the receipt image is clear and well-lit.';
        }

        $parsed = $this->parseReceiptText($rawText);

        return [
            'raw_text' => $rawText,
            'date' => $parsed['date'],
            'amount' => $parsed['amount'],
            'merchant' => $parsed['merchant'],
            'description' => $parsed['description'],
        ];
    }

    private function parseReceiptText(string $text): array
    {
        $lines = explode("\n", $text);
        $data = [
            'date' => now()->format('Y-m-d'),
            'amount' => null,
            'merchant' => 'Unknown Merchant',
            'description' => 'Receipt transaction',
        ];

        $potentialMerchantLines = array_slice($lines, 0, 5);

        foreach ($potentialMerchantLines as $line) {
            $line = trim($line);
            if (! empty($line) && strlen($line) > 2 && ! is_numeric($line)) {
                if (! preg_match('/^\d/', $line) && ! preg_match('/^\d{1,2}[\/\-]\d{1,2}/', $line)) {
                    $merchant = preg_replace('/[^a-zA-Z\s&\'-]/', '', $line);
                    $merchant = trim($merchant);
                    if (strlen($merchant) > 2) {
                        $data['merchant'] = Str::title(strtolower($merchant));
                        $data['description'] = 'Purchase at '.$data['merchant'];
                        break;
                    }
                }
            }
        }

        $datePatterns = [
            '/(\d{1,2})[\/\-](\d{1,2})[\/\-](\d{4})/',
            '/(\d{4})[\/\-](\d{1,2})[\/\-](\d{1,2})/',
            '/(\d{1,2})\s+(jan|feb|mar|apr|may|jun|jul|aug|sep|oct|nov|dec)\s+(\d{4})/i',
        ];

        foreach ($lines as $line) {
            foreach ($datePatterns as $pattern) {
                if (preg_match($pattern, $line, $matches)) {
                    try {
                        if (count($matches) >= 4) {
                            $data['date'] = Carbon::parse($matches[0])->format('Y-m-d');
                        }
                    } catch (\Exception) {
                    }
                    break 2;
                }
            }
        }

        $amountPatterns = [
            '/(?:rp|idr|rupiah|\$|usd)\s*([\d,]+(?:\.\d{2})?)/i',
            '/total\s*[:\-]?\s*(?:rp|idr|rupiah|\$|usd)?\s*([\d,]+(?:\.\d{2})?)/i',
            '/jumlah\s*[:\-]?\s*(?:rp|idr|rupiah|\$|usd)?\s*([\d,]+(?:\.\d{2})?)/i',
            '/bayar\s*[:\-]?\s*(?:rp|idr|rupiah|\$|usd)?\s*([\d,]+(?:\.\d{2})?)/i',
            '/([\d,]+(?:\.\d{2})?)\s*(?:rp|idr|rupiah|\$|usd)/i',
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
}
