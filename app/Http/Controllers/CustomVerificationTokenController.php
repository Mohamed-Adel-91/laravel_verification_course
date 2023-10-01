<?php

namespace App\Http\Controllers;

use App\Models\Merchant;
use Illuminate\Http\Request;

class CustomVerificationTokenController extends Controller
{
    public function notice(Request $request)
    {
        return $request->user('merchant')->hasVerifiedEmail()
            ? to_route('merchant.index')
            : view('merchant.auth.verify-email');
    }

    public function verify(Request $request)
    {
        $merchant = Merchant::where('verification_token', $request->token)->firstOrFail();

        if (now() < $merchant->verification_token_till) {
            $merchant->verifyUsingVerificationToken();
            return to_route('merchant.index');
        }

        abort(401);
    }

    public function resend(Request $request)
    {
        if ($request->user('merchant')->hasVerifiedEmail()) {
            return to_route('merchant.index');
        }

        $request->user('merchant')->sendEmailVerificationNotification();

        return back()->with('status', 'verification-link-sent');
    }
}
