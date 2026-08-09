@extends('layouts.app')

@section('title', 'Forgot Password')

@section('content')
<div style="max-width: 420px; margin: 3rem auto;">
    <div class="card">
        <div class="card-header" style="justify-content: center; text-align: center; font-size: 1.5rem;">
            Reset Password
        </div>
        <p style="color: var(--text-muted); font-size: 0.9rem; margin-bottom: 1.25rem; text-align: center;">
            Enter your registered email address and we will send you a password reset link.
        </p>

        <form action="{{ route('password.email') }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="email">Registered Email Address</label>
                <input type="email" id="email" name="email" class="form-control" placeholder="user@school.org" value="{{ old('email') }}" required autofocus>
            </div>

            @if(config('services.turnstile.site_key'))
                <div class="form-group" style="display: flex; justify-content: center; margin-bottom: 1.25rem;">
                    <div class="cf-turnstile" data-sitekey="{{ config('services.turnstile.site_key') }}"></div>
                </div>
                <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
            @endif

            <button type="submit" class="btn btn-primary" style="width: 100%; padding: 0.75rem;">Send Password Reset Link</button>
        </form>
    </div>

    <div style="text-align: center; color: var(--text-muted); font-size: 0.9rem;">
        Remember your password? <a href="{{ route('login') }}" style="color: var(--primary);">Return to Login</a>
    </div>
</div>
@endsection
