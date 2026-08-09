@extends('layouts.app')

@section('title', 'Set New Password')

@section('content')
<div style="max-width: 420px; margin: 3rem auto;">
    <div class="card">
        <div class="card-header" style="justify-content: center; text-align: center; font-size: 1.5rem;">
            Set New Password
        </div>
        <form action="{{ route('password.update') }}" method="POST">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">

            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" class="form-control" value="{{ request('email', old('email')) }}" required readonly style="background-color: var(--bg-card-hover);">
            </div>

            <div class="form-group">
                <label for="password">New Password</label>
                <input type="password" id="password" name="password" class="form-control" placeholder="Minimum 8 characters" required autofocus>
            </div>

            <div class="form-group">
                <label for="password_confirmation">Confirm New Password</label>
                <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" placeholder="Re-enter new password" required>
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%; padding: 0.75rem;">Reset Password</button>
        </form>
    </div>
</div>
@endsection
