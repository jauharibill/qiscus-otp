<?php

namespace App\Utils;

use App\Jobs\WhatsappVerification;
use OTPHP\TOTP;

class Helper
{
    private static $secret;

    public static function create_secret() {
        $otp = TOTP::create(null, 120);
        self::$secret = $otp->getSecret();
    }

    public static function send_whatsapp_otp($phone_number) {
        $otp = TOTP::create(self::$secret);
        WhatsappVerification::dispatch($phone_number, $otp->now());
        return "Whatsapp OTP is " . $otp->now();
    }
}
