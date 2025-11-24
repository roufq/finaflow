<?php

namespace App\Services;

class TotpService
{
    private const DEFAULT_PERIOD = 30;

    private const DEFAULT_DIGITS = 6;

    private const DEFAULT_ALGO = 'sha1';

    public function generateSecret(int $length = 16): string
    {
        $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
        $secret = '';

        for ($i = 0; $i < $length; $i++) {
            $secret .= $chars[random_int(0, strlen($chars) - 1)];
        }

        return $secret;
    }

    public function verify(string $secret, string $code, int $window = 1): bool
    {
        $code = trim($code);
        if (strlen($code) !== self::DEFAULT_DIGITS) {
            return false;
        }

        $timestamp = time();
        for ($i = -$window; $i <= $window; $i++) {
            $calculated = $this->calculateCode($secret, $timestamp + ($i * self::DEFAULT_PERIOD));
            if (hash_equals($calculated, $code)) {
                return true;
            }
        }

        return false;
    }

    public function getOtpAuthUri(string $issuer, string $accountName, string $secret): string
    {
        $label = rawurlencode($issuer).':'.rawurlencode($accountName);
        $issuerParam = rawurlencode($issuer);

        return "otpauth://totp/{$label}?secret={$secret}&issuer={$issuerParam}&period=".self::DEFAULT_PERIOD.'&digits='.self::DEFAULT_DIGITS;
    }

    private function calculateCode(string $secret, int $timestamp): string
    {
        $counter = floor($timestamp / self::DEFAULT_PERIOD);
        $binarySecret = $this->base32Decode($secret);
        $time = pack('N*', 0).pack('N*', $counter);
        $hash = hash_hmac(self::DEFAULT_ALGO, $time, $binarySecret, true);
        $offset = ord(substr($hash, -1)) & 0x0F;
        $value = unpack('N', substr($hash, $offset, 4))[1] & 0x7FFFFFFF;
        $mod = 10 ** self::DEFAULT_DIGITS;

        return str_pad((string) ($value % $mod), self::DEFAULT_DIGITS, '0', STR_PAD_LEFT);
    }

    private function base32Decode(string $secret): string
    {
        $alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
        $secret = strtoupper($secret);
        $binaryString = '';

        for ($i = 0; $i < strlen($secret); $i++) {
            $currentChar = $secret[$i];
            $index = strpos($alphabet, $currentChar);
            if ($index === false) {
                continue;
            }
            $binaryString .= str_pad(decbin($index), 5, '0', STR_PAD_LEFT);
        }

        $eightBits = str_split($binaryString, 8);
        $decoded = '';
        foreach ($eightBits as $bits) {
            if (strlen($bits) === 8) {
                $decoded .= chr(bindec($bits));
            }
        }

        return $decoded;
    }
}
