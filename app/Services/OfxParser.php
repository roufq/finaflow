<?php

namespace App\Services;

class OfxParser
{
    /**
     * Parse OFX file content and extract transactions
     */
    public function parse(string $ofxContent): array
    {
        $transactions = [];
        $errors = [];

        try {
            // Clean and normalize the OFX content
            $ofxContent = $this->normalizeOfxContent($ofxContent);

            // Extract transaction data from OFX
            $transactionData = $this->extractTransactionsFromOfx($ofxContent);

            foreach ($transactionData as $txn) {
                try {
                    $transaction = $this->mapOfxTransaction($txn);
                    if ($transaction) {
                        $transactions[] = $transaction;
                    }
                } catch (\Exception $e) {
                    $errors[] = 'Error parsing transaction: ' . $e->getMessage();
                }
            }

        } catch (\Exception $e) {
            $errors[] = 'Error parsing OFX file: ' . $e->getMessage();
        }

        return [
            'transactions' => $transactions,
            'errors' => $errors,
        ];
    }

    /**
     * Normalize OFX content by removing extra whitespace and fixing common issues
     */
    private function normalizeOfxContent(string $content): string
    {
        // Remove XML declaration if present
        $content = preg_replace('/<\?xml[^>]*\?>/i', '', $content);

        // Remove DOCTYPE if present
        $content = preg_replace('/<!DOCTYPE[^>]*>/i', '', $content);

        // Normalize line endings
        $content = str_replace(["\r\n", "\r"], "\n", $content);

        // Remove extra whitespace between tags
        $content = preg_replace('/>\s+</', '><', $content);

        return trim($content);
    }

    /**
     * Extract transaction data from OFX content
     */
    private function extractTransactionsFromOfx(string $content): array
    {
        $transactions = [];

        // Find all transaction blocks
        preg_match_all('/<STMTTRN>(.*?)<\/STMTTRN>/s', $content, $transactionBlocks);

        foreach ($transactionBlocks[1] as $block) {
            $transaction = [];

            // Extract individual fields
            $fields = [
                'TRNTYPE' => '/<TRNTYPE>(.*?)<\/TRNTYPE>/s',
                'DTPOSTED' => '/<DTPOSTED>(.*?)<\/DTPOSTED>/s',
                'TRNAMT' => '/<TRNAMT>(.*?)<\/TRNAMT>/s',
                'FITID' => '/<FITID>(.*?)<\/FITID>/s',
                'CHECKNUM' => '/<CHECKNUM>(.*?)<\/CHECKNUM>/s',
                'MEMO' => '/<MEMO>(.*?)<\/MEMO>/s',
                'NAME' => '/<NAME>(.*?)<\/NAME>/s',
                'REFNUM' => '/<REFNUM>(.*?)<\/REFNUM>/s',
            ];

            foreach ($fields as $field => $pattern) {
                if (preg_match($pattern, $block, $matches)) {
                    $transaction[$field] = trim($matches[1]);
                }
            }

            if (!empty($transaction)) {
                $transactions[] = $transaction;
            }
        }

        return $transactions;
    }

    /**
     * Map OFX transaction data to standard transaction format
     */
    private function mapOfxTransaction(array $ofxData): ?array
    {
        // Parse date
        $dateStr = $ofxData['DTPOSTED'] ?? '';
        if (empty($dateStr)) {
            return null;
        }

        $date = $this->parseOfxDate($dateStr);
        if (!$date) {
            return null;
        }

        // Parse amount
        $amountStr = $ofxData['TRNAMT'] ?? '';
        if (empty($amountStr)) {
            return null;
        }

        $amount = (float) $amountStr;
        if ($amount == 0) {
            return null;
        }

        // Determine transaction type
        $type = $amount > 0 ? 'income' : 'expense';
        $amount = abs($amount);

        // Build description
        $description = '';
        if (!empty($ofxData['NAME'])) {
            $description = $ofxData['NAME'];
        } elseif (!empty($ofxData['MEMO'])) {
            $description = $ofxData['MEMO'];
        } else {
            $description = 'OFX Transaction';
        }

        // Add check number if present
        if (!empty($ofxData['CHECKNUM'])) {
            $description .= ' (Check #' . $ofxData['CHECKNUM'] . ')';
        }

        return [
            'date' => $date->format('Y-m-d'),
            'description' => $description,
            'amount' => $amount,
            'type' => $type,
            'reference_id' => $ofxData['FITID'] ?? null,
            'raw_data' => $ofxData,
        ];
    }

    /**
     * Parse OFX date format (YYYYMMDD or YYYYMMDDHHMMSS)
     */
    private function parseOfxDate(string $dateStr): ?\Carbon\Carbon
    {
        $dateStr = trim($dateStr);

        try {
            // OFX dates are in format YYYYMMDD or YYYYMMDDHHMMSS
            if (strlen($dateStr) >= 8) {
                $year = substr($dateStr, 0, 4);
                $month = substr($dateStr, 4, 2);
                $day = substr($dateStr, 6, 2);

                return \Carbon\Carbon::createFromFormat('Y-m-d', "{$year}-{$month}-{$day}");
            }
        } catch (\Exception $e) {
            // Ignore and return null
        }

        return null;
    }
}
