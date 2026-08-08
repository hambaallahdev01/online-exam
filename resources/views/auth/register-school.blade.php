@extends('layouts.app')

@section('title', 'Register School')

@section('content')
<div style="max-width: 540px; margin: 2rem auto;">
    <div class="card">
        <div class="card-header" style="justify-content: center; font-size: 1.4rem;">
            Register School Account
        </div>
        <form action="{{ route('register.school') }}" method="POST">
            @csrf
            <h3 style="font-size: 1rem; color: var(--primary); margin-bottom: 1rem;">1. School Information</h3>
            <div class="form-group">
                <label for="school_name">School Name</label>
                <input type="text" id="school_name" name="school_name" class="form-control" placeholder="e.g. SMAN 1 Jakarta" value="{{ old('school_name') }}" required>
            </div>
            <div class="form-group">
                <label for="school_code">School Code (Optional)</label>
                <input type="text" id="school_code" name="school_code" class="form-control" placeholder="e.g. SCH001" value="{{ old('school_code') }}">
            </div>
            <div class="form-group">
                <label for="school_email">School Email</label>
                <input type="email" id="school_email" name="school_email" class="form-control" placeholder="info@school.sch.id" value="{{ old('school_email') }}" required>
            </div>

            <h3 style="font-size: 1rem; color: var(--primary); margin: 1.5rem 0 1rem;">2. Administrator Account</h3>
            <div class="form-group">
                <label for="admin_name">Admin Full Name</label>
                <input type="text" id="admin_name" name="admin_name" class="form-control" placeholder="Administrator Name" value="{{ old('admin_name') }}" required>
            </div>
            <div class="form-group">
                <label for="admin_email">Admin Email Address</label>
                <input type="email" id="admin_email" name="admin_email" class="form-control" placeholder="admin@school.sch.id" value="{{ old('admin_email') }}" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" class="form-control" placeholder="Minimum 8 characters" required>
            </div>
            <div class="form-group">
                <label for="password_confirmation">Confirm Password</label>
                <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" placeholder="Repeat password" required>
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%; padding: 0.8rem;">Complete School Registration</button>
        </form>
    </div>
</div>
@endsection
