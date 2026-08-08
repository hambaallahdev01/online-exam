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
        <div class="card-header">
            <span>School Identity Info</span>
        </div>
        <p><strong>School Code:</strong> {{ $school->code ?? 'N/A' }}</p>
        <p><strong>Email:</strong> {{ $school->email }}</p>
        <p><strong>Address:</strong> {{ $school->address ?? 'Not specified' }}</p>
    </div>
</div>
@endsection
