<?php

declare(strict_types=1);

namespace App\Services\Implementations;

use ParagonIE\ConstantTime\Base32;

class TOTPService
{
    // In seconds
    private const TIME_STEP = 30;
    private const TOKEN_LENGTH = 6;

    private function getNumberOfStepsElapsedSinceUnix(): int
    {
        return intval(floor(time() / self::TIME_STEP));
    }

    public function generateSharedSecret(): string
    {
        return Base32::encode(random_bytes(20));
    }

    public function generateOTPToken(string $secret): string
    {
        $hmac = hash_hmac(
            "sha1",
            pack('J', $this->getNumberOfStepsElapsedSinceUnix()),
            Base32::decodeUpper($secret),
            true
        );

        // Values in bytes ( 20 )
        $hmac = array_values(unpack("C*", $hmac));

        // Get last 4 bits
        $offset = ($hmac[count($hmac) - 1] & 0x0F);

        $code =
            ($hmac[$offset] & 0x7F)     << 24 |
            ($hmac[$offset + 1] & 0xFF) << 16 |
            ($hmac[$offset + 2] & 0xFF) << 8  |
            ($hmac[$offset + 3] & 0xFF);


        $otp = $code % (pow(10, self::TOKEN_LENGTH));

        return str_pad((string) $otp, self::TOKEN_LENGTH, "0", STR_PAD_LEFT);
    }
}
