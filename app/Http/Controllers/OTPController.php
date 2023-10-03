<?php

namespace App\Http\Controllers;

use App\Models\Merchant;
use App\Services\Twilio;
use App\Services\Vonage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class OTPController extends Controller
{
    public function store(Request $request)
    {
        // validate email
        $request->validate([
            'email' => ['required', 'email', 'max:255'],
        ]);

        // check if email exists on database
        $merchant = Merchant::where('email', $request->email)->first();

        // if not exists -> throw validation error
        if (!$merchant) {
            throw ValidationException::withMessages([
                'email' => trans('auth.failed'),
            ]);
        }

        // if exists -> send email signed url
        $merchant->generateOTP();

        // send OTP to SMS using provider
        if (config('verification.otp_provider') == 'twilio') {
            (new Twilio())->send($merchant);
        }
        if (config('verification.otp_provider') == 'vonage') {
            (new Vonage())->send($merchant);
        }

        // return back with status message
        return view('merchant.auth.verify-otp', ['email' => $request->email]);
    }

    public function verify(Request $request)
    {
        // validate email & otp
        $request->validate([
            'email' => ['required', 'email', 'max:255'],
            'otp' => ['required'],
        ]);

        // check if email exists on database
        $merchant = Merchant::where('email', $request->email)->first();

        // if not exists -> throw validation error
        if (!$merchant) {
            throw ValidationException::withMessages([
                'email' => trans('auth.failed'),
            ]);
        }

        // if exists -> send email signed url
        if ($merchant && $merchant->otp == $request->otp) {
            if (now() < $merchant->otp_till) {
                $merchant->resetOTP();
                Auth::guard('merchant')->login($merchant);
                return to_route('merchant.index');
            } else {
                throw ValidationException::withMessages([
                    'email' => 'Expired OTP',
                ]);
            }
        }
    }
}
