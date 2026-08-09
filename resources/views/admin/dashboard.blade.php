@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
<div style="margin-bottom: 2rem;">
    <h1 style="font-size: 1.8rem;">School Overview: {{ $school->name }}</h1>
    <p style="color: var(--text-muted);">Manage academic records, teachers, and students.</p>
</div>

<div class="grid-stats">
    <div class="stat-box">
        <div style="color: var(--text-muted); font-size: 0.9rem;">Total Teachers</div>
        <div class="stat-number">{{ $teachersCount }}</div>
    </div>
    <div class="stat-box">
        <div style="color: var(--text-muted); font-size: 0.9rem;">Total Students</div>
        <div class="stat-number" style="color: var(--accent);">{{ $studentsCount }}</div>
    </div>
    <div class="stat-box">
        <div style="color: var(--text-muted); font-size: 0.9rem;">Classrooms</div>
        <div class="stat-number" style="color: var(--warning);">{{ $classroomsCount }}</div>
    </div>
    <div class="stat-box">
        <div style="color: var(--text-muted); font-size: 0.9rem;">Subjects</div>
        <div class="stat-number" style="color: #ec4899;">{{ $subjectsCount }}</div>
    </div>
</div>

<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
    <div class="card">
        <div class="card-header">
            <span>Quick Actions</span>
        </div>
        <div style="display: flex; flex-direction: column; gap: 0.75rem;">
            <a href="{{ route('admin.teachers') }}" class="btn btn-secondary" style="justify-content: flex-start;"><i class="fa-solid fa-chalkboard-user"></i>&nbsp; Manage Teachers</a>
            <a href="{{ route('admin.students') }}" class="btn btn-secondary" style="justify-content: flex-start;"><i class="fa-solid fa-user-graduate"></i>&nbsp; Manage Students</a>
        </div>
    </div>

    <div class="card">
        <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
            <span>School Identity Info</span>
            <button type="button" class="btn btn-secondary" style="padding: 0.3rem 0.75rem; font-size: 0.85rem;" onclick="document.getElementById('editSchoolProfileForm').style.display = document.getElementById('editSchoolProfileForm').style.display === 'none' ? 'block' : 'none';">
                <i class="fa-solid fa-pen-to-square"></i> Edit Profile
            </button>
        </div>
        <div style="line-height: 1.8;">
            <p><strong>School Name:</strong> {{ $school->name }}</p>
            <p><strong>School Code:</strong> <span style="background: var(--bg-card-hover); padding: 0.2rem 0.5rem; border-radius: 0.3rem; font-family: monospace;">{{ $school->code ?? 'N/A' }}</span></p>
            <p><strong>Email:</strong> {{ $school->email }}</p>
            <p><strong>Phone:</strong> {{ $school->phone ?? 'Not specified' }}</p>
            <p><strong>Address:</strong> {{ $school->address ?? 'Not specified' }}</p>
        </div>

        <!-- Edit Form Collapsible -->
        <div id="editSchoolProfileForm" style="display: none; margin-top: 1.25rem; padding-top: 1.25rem; border-top: 1px solid var(--border-color);">
            <h4 style="margin-bottom: 1rem; color: var(--primary); font-size: 1.1rem;">Update School Profile</h4>
            <form action="{{ route('admin.profile.update') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label>School Name</label>
                    <input type="text" name="name" class="form-control" value="{{ old('name', $school->name) }}" required>
                </div>
                <div class="form-group">
                    <label>School Email</label>
                    <input type="email" name="email" class="form-control" value="{{ old('email', $school->email) }}" required>
                </div>
                <div class="form-group">
                    <label>Phone Number</label>
                    <input type="text" name="phone" class="form-control" value="{{ old('phone', $school->phone) }}" placeholder="e.g. (021) 555-1234">
                </div>
                <div class="form-group">
                    <label>School Address</label>
                    <textarea name="address" class="form-control" rows="3" placeholder="Enter complete school address...">{{ old('address', $school->address) }}</textarea>
                </div>
                <div style="display: flex; gap: 0.5rem; justify-content: flex-end;">
                    <button type="button" class="btn btn-secondary" onclick="document.getElementById('editSchoolProfileForm').style.display = 'none';">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
