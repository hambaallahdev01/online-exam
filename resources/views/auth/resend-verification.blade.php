@extends('layouts.app')

@section('title', 'Resend Email Verification')

@section('content')
<div style="max-width: 480px; margin: 3rem auto;">
    <div class="card">
        <div class="card-header" style="justify-content: center; text-align: center; font-size: 1.5rem;">
            Email Verification Help
        </div>

        <div style="display: flex; gap: 0.5rem; margin-bottom: 1.25rem; border-bottom: 1px solid var(--border-color); padding-bottom: 0.5rem;">
            <button type="button" id="tabResendBtn" onclick="switchTab('resend')" style="flex: 1; padding: 0.5rem; border: none; background: var(--primary); color: white; font-weight: 600; border-radius: 0.4rem; cursor: pointer;">
                Resend Link
            </button>
            <button type="button" id="tabChangeBtn" onclick="switchTab('change')" style="flex: 1; padding: 0.5rem; border: 1px solid var(--border-color); background: transparent; color: var(--text-main); font-weight: 600; border-radius: 0.4rem; cursor: pointer;">
                Change Email Address
            </button>
        </div>

        <!-- Form 1: Resend Link to Existing Email -->
        <div id="formResendBox">
            <p style="color: var(--text-muted); font-size: 0.9rem; margin-bottom: 1.25rem; text-align: center;">
                Enter your registered email address to receive a new verification link.
            </p>
            <form action="{{ route('verification.resend') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label for="email">Registered Email Address</label>
                    <input type="email" id="email" name="email" class="form-control" placeholder="user@school.org" value="{{ request('email', old('email')) }}" required autofocus>
                </div>

                @if(config('services.turnstile.site_key'))
                    <div class="form-group" style="display: flex; justify-content: center; margin-bottom: 1.25rem;">
                        <div class="cf-turnstile" data-sitekey="{{ config('services.turnstile.site_key') }}"></div>
                    </div>
                @endif

                <button type="submit" class="btn btn-primary" style="width: 100%; padding: 0.75rem;">Resend Verification Email</button>
            </form>
        </div>

        <!-- Form 2: Change Unverified Email (e.g. Yahoo -> Gmail) -->
        <div id="formChangeBox" style="display: none;">
            <p style="color: var(--text-muted); font-size: 0.9rem; margin-bottom: 1.25rem; text-align: center;">
                Registered with wrong/bouncing email (e.g. Yahoo)? Enter your old email, password, and new email address (e.g. Gmail).
            </p>
            <form action="{{ route('verification.change.resend') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label for="old_email">Old Registered Email (e.g. Yahoo)</label>
                    <input type="email" id="old_email" name="old_email" class="form-control" placeholder="old@yahoo.co.id" required>
                </div>

                <div class="form-group">
                    <label for="password">Account Password</label>
                    <input type="password" id="password" name="password" class="form-control" placeholder="••••••••" required>
                </div>

                <div class="form-group">
                    <label for="new_email">New Email Address (e.g. Gmail)</label>
                    <input type="email" id="new_email" name="new_email" class="form-control" placeholder="new@gmail.com" required>
                </div>

                @if(config('services.turnstile.site_key'))
                    <div class="form-group" style="display: flex; justify-content: center; margin-bottom: 1.25rem;">
                        <div class="cf-turnstile" data-sitekey="{{ config('services.turnstile.site_key') }}"></div>
                    </div>
                @endif

                <button type="submit" class="btn btn-accent" style="width: 100%; padding: 0.75rem;">Update Email & Send Verification Link</button>
            </form>
        </div>

        @if(config('services.turnstile.site_key'))
            <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
        @endif
    </div>

    <div style="text-align: center; color: var(--text-muted); font-size: 0.9rem;">
        Already verified? <a href="{{ route('login') }}" style="color: var(--primary);">Return to Login</a>
    </div>
</div>

<script>
    function switchTab(tab) {
        const resendBox = document.getElementById('formResendBox');
        const changeBox = document.getElementById('formChangeBox');
        const resendBtn = document.getElementById('tabResendBtn');
        const changeBtn = document.getElementById('tabChangeBtn');

        if (tab === 'change') {
            resendBox.style.display = 'none';
            changeBox.style.display = 'block';
            resendBtn.style.background = 'transparent';
            resendBtn.style.color = 'var(--text-main)';
            resendBtn.style.border = '1px solid var(--border-color)';
            changeBtn.style.background = 'var(--primary)';
            changeBtn.style.color = 'white';
            changeBtn.style.border = 'none';
        } else {
            resendBox.style.display = 'block';
            changeBox.style.display = 'none';
            changeBtn.style.background = 'transparent';
            changeBtn.style.color = 'var(--text-main)';
            changeBtn.style.border = '1px solid var(--border-color)';
            resendBtn.style.background = 'var(--primary)';
            resendBtn.style.color = 'white';
            resendBtn.style.border = 'none';
        }
    }
</script>
@endsection
