<?php

return [
    // VERIFICATION TECHNIQUES
    // 'default' => Without any verification
    // 'email' => With email verification using signed URLs (register)
    // 'cvt' => With email verification using custom verification token (register)
    // 'passwordless' => passwordless authentication (login)
    // 'otp' => OTP authentication  (login)

    'way' => 'default',

    // OTP PROVIDERS
    // 'twilio' OR 'vonage'
    'otp_provider' => 'vonage'
];
