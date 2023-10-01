<?php

namespace App\Http\Controllers;

use App\Models\Merchant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class PasswordlessAuthController extends Controller
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
        $merchant->sendEmailVerificationNotification();

        // return back with status message
        return back()->with('status', 'Link sent to your inbox');
    }

    public function verify($merchant)
    {
        Auth::guard('merchant')->loginUsingId($merchant);
        return to_route('merchant.index');
    }
}
