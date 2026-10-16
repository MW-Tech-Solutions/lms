<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class TwoFactorAuthService
{
    private const BASE32_CHARS = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';

    /**
     * Generate a 16-character RFC 3548 Base32 Secret Key for Google Authenticator.
     */
    public function generateSecret(int $length = 16): string
    {
        $secret = '';
        for ($i = 0; $i < $length; $i++) {
            $secret .= self::BASE32_CHARS[random_int(0, 31)];
        }
        return $secret;
    }

    /**
     * Build an otpauth URL for Google Authenticator QR Code.
     */
    public function getOtpAuthUrl(User $user, string $secret, string $issuer = 'Edunex LMS'): string
    {
        $label = urlencode($issuer . ':' . $user->email);
        $issuerEncoded = urlencode($issuer);

        return "otpauth://totp/{$label}?secret={$secret}&issuer={$issuerEncoded}&algorithm=SHA1&digits=6&period=30";
    }

    /**
     * Build an image QR code URL for rendering the otpauth barcode.
     */
    public function getQrCodeUrl(string $email, string $secret, string $issuer = 'AllThingsCambridge'): string
    {
        $label = urlencode($issuer . ':' . $email);
        $issuerEncoded = urlencode($issuer);
        $otpauth = "otpauth://totp/{$label}?secret={$secret}&issuer={$issuerEncoded}&algorithm=SHA1&digits=6&period=30";

        // Generate QR code using Google Chart / QR Server reliable API
        return 'https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=' . urlencode($otpauth);
    }

    /**
     * Verify a 6-digit TOTP code against a Base32 secret (RFC 6238).
     */
    public function verifyTotp(string $secret, string $code): bool
    {
        $code = trim($code);
        if (strlen($code) !== 6 || !ctype_digit($code)) {
            return false;
        }

        $secret = strtoupper(str_replace(' ', '', $secret));
        $binaryKey = $this->base32Decode($secret);
        if ($binaryKey === '') {
            return false;
        }

        $currentTimeSlice = (int) floor(time() / 30);

        // Check window: previous, current, next (+/- 30 seconds for clock drift)
        for ($offset = -1; $offset <= 1; $offset++) {
            $calculatedCode = $this->calculateCode($binaryKey, $currentTimeSlice + $offset);
            if (hash_equals($calculatedCode, $code)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Generate and dispatch a 6-digit verification code via Email.
     */
    public function sendEmailOtp(User $user): string
    {
        $code = (string) random_int(100000, 999999);

        $user->forceFill([
            'two_factor_email_code' => $code,
            'two_factor_expires_at' => now()->addMinutes(10),
        ])->save();

        Log::info(sprintf('2FA Email OTP dispatched to user %s (%s): %s', $user->id, $user->email, $code));

        try {
            // Send styled HTML email if mail driver configured
            Mail::send(
                'emails.two_factor_otp',
                [
                    'user' => $user,
                    'code' => $code,
                    'subject' => 'Your 2-Factor Authentication Security Code',
                ],
                function ($message) use ($user) {
                    $message->to($user->email)
                        ->subject('Your 2-Factor Authentication Security Code');
                }
            );
        } catch (\Throwable $e) {
            Log::warning('Mail delivery failed for 2FA, logged to system audit: ' . $e->getMessage());
        }

        return $code;
    }

    /**
     * Verify the 6-digit email OTP.
     */
    public function verifyEmailOtp(User $user, string $code): bool
    {
        $code = trim($code);
        if (!$user->two_factor_email_code || !$user->two_factor_expires_at) {
            return false;
        }

        if (now()->greaterThan($user->two_factor_expires_at)) {
            return false;
        }

        if (hash_equals($user->two_factor_email_code, $code)) {
            // Consume OTP immediately to prevent replay
            $user->forceFill([
                'two_factor_email_code' => null,
                'two_factor_expires_at' => null,
            ])->save();

            return true;
        }

        return false;
    }

    /**
     * Base32 RFC 4648 decoder.
     */
    private function base32Decode(string $b32): string
    {
        $b32 = strtoupper($b32);
        $lut = [
            'A' => 0,  'B' => 1,  'C' => 2,  'D' => 3,
            'E' => 4,  'F' => 5,  'G' => 6,  'H' => 7,
            'I' => 8,  'J' => 9,  'K' => 10, 'L' => 11,
            'M' => 12, 'N' => 13, 'O' => 14, 'P' => 15,
            'Q' => 16, 'R' => 17, 'S' => 18, 'T' => 19,
            'U' => 20, 'V' => 21, 'W' => 22, 'X' => 23,
            'Y' => 24, 'Z' => 25, '2' => 26, '3' => 27,
            '4' => 28, '5' => 29, '6' => 30, '7' => 31,
        ];

        $buffer = 0;
        $bitsLeft = 0;
        $output = '';

        for ($i = 0, $len = strlen($b32); $i < $len; $i++) {
            $char = $b32[$i];
            if (!isset($lut[$char])) {
                continue;
            }
            $buffer = ($buffer << 5) | $lut[$char];
            $bitsLeft += 5;

            if ($bitsLeft >= 8) {
                $bitsLeft -= 8;
                $output .= chr(($buffer >> $bitsLeft) & 0xFF);
            }
        }

        return $output;
    }

    /**
     * Calculate 6-digit TOTP code for a binary key and time slice.
     */
    private function calculateCode(string $binaryKey, int $timeSlice): string
    {
        $data = pack('N*', 0) . pack('N*', $timeSlice);
        $hmac = hash_hmac('sha1', $data, $binaryKey, true);

        $offset = ord(substr($hmac, -1)) & 0x0F;
        $hashPart = substr($hmac, $offset, 4);

        $value = unpack('N', $hashPart)[1] & 0x7FFFFFFF;
        $modulo = $value % 1000000;

        return str_pad((string) $modulo, 6, '0', STR_PAD_LEFT);
    }
}
