<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\URL;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Notifications\MerchantEmailVerification;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Merchant extends Authenticatable implements MustVerifyEmail
{
    public function sendEmailVerificationNotification()
    {
        if (config('verification.way') == 'email') {
            $url = URL::temporarySignedRoute(
                'merchant.verification.verify',
                now()->addMinutes(60),
                [
                    'id' => $this->getKey(),
                    'hash' => sha1($this->getEmailForVerification()),
                ]
            );
            $this->notify(new MerchantEmailVerification($url));
        }

        if (config('verification.way') == 'cvt') {
            $this->generateVerificationToken();
            $url = route('merchant.verification.verify', [
                'id' => $this->getKey(),
                'token' => $this->verification_token,
            ]);
            $this->notify(new MerchantEmailVerification($url));
        }

        if (config('verification.way') == 'passwordless') {
            $url = URL::temporarySignedRoute(
                'merchant.login.verify',
                now()->addMinutes(60),
                [
                    'merchant' => $this->getKey(),
                ]
            );
            $this->notify(new MerchantEmailVerification($url));
        }
    }

    // ====================================== CUSTOM VERIFICATION TOKENS
    public function generateVerificationToken()
    {
        if (config('verification.way') == 'cvt') {
            $this->verification_token = Str::random(40);
            $this->verification_token_till = now()->addMinutes(1);
            $this->save();
        }
    }

    public function verifyUsingVerificationToken()
    {
        if (config('verification.way') == 'cvt') {
            $this->email_verified_at = now();
            $this->verification_token = null;
            $this->verification_token_till = null;
            $this->save();
        }
    }
    // ====================================== CUSTOM VERIFICATION TOKENS



    use HasFactory, Notifiable;

    protected $guarded = ['id'];
}
