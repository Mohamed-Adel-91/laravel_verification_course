@extends('merchant.auth.master')

@section('title', 'Login')
@section('content')
    <div class="card">
        <div class="card-body">
            @include('merchant.auth.logo')

            <h4 class="mb-2">Welcome to Sneat! 👋</h4>
            <p class="mb-4">Please Verify your OTP</p>

            <!-- Session Status -->
            <x-auth-session-status class="mb-4 text-success" :status="session('status')" />

            <form id="formAuthentication" class="mb-3" action="{{ route('merchant.verifyOTP') }}" method="POST">
                @csrf
                <input type="hidden" name="email" value="{{ $email }}">

                <div class="mb-3">
                    <label for="email" class="form-label">OTP</label>
                    <input type="text" class="form-control" id="otp" name="otp" placeholder="Enter your OTP"
                        autofocus value="{{ old('otp') }}" />
                    <x-input-error :messages="$errors->get('otp')" class="mt-2" />
                </div>

                <div class="mb-3">
                    <button class="btn btn-primary d-grid w-100" type="submit">Sign in</button>
                </div>
            </form>

            <p class="text-center">
                <span>New on our platform?</span>
                <a href="{{ route('merchant.register') }}">
                    <span>Create an account</span>
                </a>
            </p>
        </div>
    </div>
@endsection
