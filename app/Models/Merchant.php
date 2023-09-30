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

    use HasFactory, Notifiable;

    protected $guarded = ['id'];
}
