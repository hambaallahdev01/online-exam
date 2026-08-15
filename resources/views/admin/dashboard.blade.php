@extends('layouts.app')

@section('title', __('messages.dashboard'))

@section('content')
<div style="margin-bottom: 2rem;">
    <h1 style="font-size: 1.8rem;">{{ __('messages.school_overview') }}: {{ $school->name }}</h1>
    <p style="color: var(--text-muted);">{{ __('messages.manage_overview_desc') }}</p>
</div>

<div class="grid-stats">
    <div class="stat-box">
        <div style="color: var(--text-muted); font-size: 0.9rem;">{{ __('messages.total_teachers') }}</div>
        <div class="stat-number">{{ $teachersCount }}</div>
    </div>
    <div class="stat-box">
        <div style="color: var(--text-muted); font-size: 0.9rem;">{{ __('messages.total_students') }}</div>
        <div class="stat-number" style="color: var(--accent);">{{ $studentsCount }}</div>
    </div>
    <div class="stat-box">
        <div style="color: var(--text-muted); font-size: 0.9rem;">{{ __('messages.classrooms') }}</div>
        <div class="stat-number" style="color: var(--warning);">{{ $classroomsCount }}</div>
    </div>
    <div class="stat-box">
        <div style="color: var(--text-muted); font-size: 0.9rem;">{{ __('messages.subjects') }}</div>
        <div class="stat-number" style="color: #ec4899;">{{ $subjectsCount }}</div>
    </div>
</div>

<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
    <div class="card">
        <div class="card-header">
            <span>{{ __('messages.quick_actions') }}</span>
        </div>
        <div style="display: flex; flex-direction: column; gap: 0.75rem;">
            <a href="{{ route('admin.teachers') }}" class="btn btn-secondary" style="justify-content: flex-start;"><i class="fa-solid fa-chalkboard-user"></i>&nbsp; {{ __('messages.manage_teachers') }}</a>
            <a href="{{ route('admin.students') }}" class="btn btn-secondary" style="justify-content: flex-start;"><i class="fa-solid fa-user-graduate"></i>&nbsp; {{ __('messages.manage_students') }}</a>
        </div>
    </div>

    <div class="card">
        <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
            <span>{{ __('messages.school_identity') }}</span>
            <button type="button" class="btn btn-secondary" style="padding: 0.3rem 0.75rem; font-size: 0.85rem;" onclick="document.getElementById('editSchoolProfileForm').style.display = document.getElementById('editSchoolProfileForm').style.display === 'none' ? 'block' : 'none';">
                <i class="fa-solid fa-pen-to-square"></i> {{ __('messages.edit_profile') }}
            </button>
        </div>
        <div style="line-height: 1.8;">
            <p><strong>{{ __('messages.school_name') }}:</strong> {{ $school->name }}</p>
            <p><strong>{{ __('messages.school_code') }}:</strong> <span style="background: var(--bg-card-hover); padding: 0.2rem 0.5rem; border-radius: 0.3rem; font-family: monospace;">{{ $school->code ?? __('messages.not_specified') }}</span></p>
            <p><strong>{{ __('messages.email') }}:</strong> {{ $school->email }}</p>
            <p><strong>{{ __('messages.phone') }}:</strong> {{ $school->phone ?? __('messages.not_specified') }}</p>
            <p><strong>{{ __('messages.address') }}:</strong> {{ $school->address ?? __('messages.not_specified') }}</p>
            <p>
                <strong>{{ __('messages.language_setting') }}:</strong>
                <span class="badge" style="background: rgba(99, 102, 241, 0.15); color: var(--primary); padding: 0.25rem 0.6rem; border-radius: 0.4rem; font-weight: 600;">
                    @if(($school->locale ?? 'id') === 'id') 🇮🇩 {{ __('messages.lang_id') }}
                    @elseif($school->locale === 'en') 🇬🇧 {{ __('messages.lang_en') }}
                    @elseif($school->locale === 'ar') 🇸🇦 {{ __('messages.lang_ar') }}
                    @elseif($school->locale === 'zh') 🇨🇳 {{ __('messages.lang_zh') }}
                    @else {{ $school->locale }}
                    @endif
                </span>
            </p>
            <p>
                <strong>{{ __('messages.timezone_setting') }}:</strong>
                <span class="badge" style="background: rgba(14, 165, 233, 0.15); color: #38bdf8; padding: 0.25rem 0.6rem; border-radius: 0.4rem; font-weight: 600;">
                    {{ $schoolTimezone }}
                </span>
            </p>
        </div>

        <!-- Edit Form Collapsible -->
        <div id="editSchoolProfileForm" style="display: none; margin-top: 1.25rem; padding-top: 1.25rem; border-top: 1px solid var(--border-color);">
            <h4 style="margin-bottom: 1rem; color: var(--primary); font-size: 1.1rem;">{{ __('messages.update_school_profile') }}</h4>
            <form action="{{ route('admin.profile.update') }}" method="POST">
                @csrf
                <div class="form-group" style="margin-bottom: 1rem;">
                    <label style="display: block; margin-bottom: 0.3rem; font-weight: 500;">{{ __('messages.school_name') }}</label>
                    <input type="text" name="name" class="form-control" style="width: 100%; padding: 0.5rem; border-radius: 0.4rem; border: 1px solid var(--border-color);" value="{{ old('name', $school->name) }}" required>
                </div>
                <div class="form-group" style="margin-bottom: 1rem;">
                    <label style="display: block; margin-bottom: 0.3rem; font-weight: 500;">{{ __('messages.email') }}</label>
                    <input type="email" name="email" class="form-control" style="width: 100%; padding: 0.5rem; border-radius: 0.4rem; border: 1px solid var(--border-color);" value="{{ old('email', $school->email) }}" required>
                </div>
                <div class="form-group" style="margin-bottom: 1rem;">
                    <label style="display: block; margin-bottom: 0.3rem; font-weight: 500;">{{ __('messages.phone') }}</label>
                    <input type="text" name="phone" class="form-control" style="width: 100%; padding: 0.5rem; border-radius: 0.4rem; border: 1px solid var(--border-color);" value="{{ old('phone', $school->phone) }}" placeholder="e.g. (021) 555-1234">
                </div>
                <div class="form-group" style="margin-bottom: 1rem;">
                    <label style="display: block; margin-bottom: 0.3rem; font-weight: 500;">{{ __('messages.address') }}</label>
                    <textarea name="address" class="form-control" rows="3" style="width: 100%; padding: 0.5rem; border-radius: 0.4rem; border: 1px solid var(--border-color);" placeholder="Enter complete school address...">{{ old('address', $school->address) }}</textarea>
                </div>
                <div class="form-group" style="margin-bottom: 1.25rem;">
                    <label style="display: block; margin-bottom: 0.3rem; font-weight: 500;">{{ __('messages.language_setting') }}</label>
                    <select name="locale" class="form-control" style="width: 100%; padding: 0.5rem; border-radius: 0.4rem; border: 1px solid var(--border-color); background: var(--bg-card); color: var(--text-main);" required>
                        <option value="id" {{ old('locale', $school->locale ?? 'id') === 'id' ? 'selected' : '' }}>🇮🇩 {{ __('messages.lang_id') }}</option>
                        <option value="en" {{ old('locale', $school->locale ?? '') === 'en' ? 'selected' : '' }}>🇬🇧 {{ __('messages.lang_en') }}</option>
                        <option value="ar" {{ old('locale', $school->locale ?? '') === 'ar' ? 'selected' : '' }}>🇸🇦 {{ __('messages.lang_ar') }}</option>
                        <option value="zh" {{ old('locale', $school->locale ?? '') === 'zh' ? 'selected' : '' }}>🇨🇳 {{ __('messages.lang_zh') }}</option>
                    </select>
                    <small style="display: block; color: var(--text-muted); margin-top: 0.3rem; font-size: 0.8rem;">
                        Pengaturan ini akan otomatis diterapkan ke seluruh akun Guru dan Siswa pada sekolah/instansi ini saat berada di dalam sistem ujian.
                    </small>
                </div>
                <div class="form-group" style="margin-bottom: 1.25rem;">
                    <label style="display: block; margin-bottom: 0.3rem; font-weight: 500;">{{ __('messages.timezone_setting') }}</label>
                    <select name="timezone" class="form-control" style="width: 100%; padding: 0.5rem; border-radius: 0.4rem; border: 1px solid var(--border-color); background: var(--bg-card); color: var(--text-main);" required>
                        @foreach($timezoneOptions as $identifier => $label)
                            <option value="{{ $identifier }}" {{ old('timezone', $schoolTimezone) === $identifier ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                    <small style="display: block; color: var(--text-muted); margin-top: 0.3rem; font-size: 0.8rem;">
                        {{ __('messages.timezone_change_help') }}
                    </small>
                </div>
                <div style="display: flex; gap: 0.5rem; justify-content: flex-end;">
                    <button type="button" class="btn btn-secondary" onclick="document.getElementById('editSchoolProfileForm').style.display = 'none';">{{ __('messages.cancel') }}</button>
                    <button type="submit" class="btn btn-primary">{{ __('messages.save_changes') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
