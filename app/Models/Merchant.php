<?php

namespace App\Models;

use App\Notifications\MerchantEmailVerification;
use Illuminate\Support\Facades\URL;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
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
    }

    // ====================================== CUSTOM VERIFICATION TOKENS
    public function generateVerificationToken()
    {
        if (config('verification.way') == 'cvt') {
            $this->verification_token = Str::random(40);
            $this->verification_token_till = now()->addMinutes(10);
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
