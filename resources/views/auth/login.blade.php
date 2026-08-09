@extends('layouts.app')

@section('title', 'Login')

@section('content')
<div style="max-width: 420px; margin: 3rem auto;">
    <div class="card">
        <div class="card-header" style="justify-content: center; text-align: center; font-size: 1.5rem;">
            Sign In to Platform
        </div>
        <form action="{{ route('login') }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" class="form-control" placeholder="user@school.org" value="{{ old('email') }}" required autofocus>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" class="form-control" placeholder="••••••••" required>
            </div>
            <div class="form-group" style="display: flex; align-items: center; justify-content: space-between;">
                <div style="display: flex; align-items: center; gap: 0.5rem;">
                    <input type="checkbox" id="remember" name="remember">
                    <label for="remember" style="margin-bottom: 0;">Remember me</label>
                </div>
                <a href="{{ route('password.request') }}" style="font-size: 0.85rem; color: var(--accent); text-decoration: underline;">Forgot Password?</a>
            </div>
            @if(config('services.turnstile.site_key'))
                <div class="form-group" style="display: flex; justify-content: center; margin-bottom: 1.25rem;">
                    <div class="cf-turnstile" data-sitekey="{{ config('services.turnstile.site_key') }}"></div>
                </div>
                <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
            @endif
            <button type="submit" class="btn btn-primary" style="width: 100%; padding: 0.75rem;">Sign In</button>
        </form>
    </div>

    <div style="text-align: center; color: var(--text-muted); font-size: 0.9rem; display: flex; flex-direction: column; gap: 0.4rem;">
        <div>New School? <a href="{{ route('register.school') }}" style="color: var(--primary);">Register your School Account</a></div>
        <div>Didn't receive verification email? <a href="{{ route('verification.resend.show') }}" style="color: var(--accent); text-decoration: underline;">Resend Verification Link</a></div>
    </div>
</div>
@endsection
