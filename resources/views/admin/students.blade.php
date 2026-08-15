@extends('layouts.app')

@section('title', 'Manage Students')

@section('content')
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
    <div>
        <h1 style="font-size: 1.6rem;">Student Management</h1>
        <p style="color: var(--text-muted);">Add and manage student accounts.</p>
    </div>
</div>

<div style="display: grid; grid-template-columns: 1fr 2fr; gap: 1.5rem;">
    <div class="card">
        <div class="card-header">Register Student</div>
        <form action="{{ route('admin.students') }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="name">Full Name</label>
                <input type="text" name="name" class="form-control" placeholder="John Doe" required>
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" name="email" class="form-control" placeholder="student@school.org" required>
            </div>
            <div class="form-group">
                <label for="identity_number">NIS / Student ID</label>
                <input type="text" name="identity_number" class="form-control" placeholder="NIS2026...">
            </div>
            <div class="form-group">
                <label for="password">Default Password</label>
                <input type="password" name="password" class="form-control" placeholder="••••••••" required>
            </div>
            <button type="submit" class="btn btn-primary" style="width: 100%;">Add Student</button>
        </form>
    </div>

    <div class="card">
        <div class="card-header">Registered Students</div>
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>NIS / ID</th>
                    <th>Joined</th>
                </tr>
            </thead>
            <tbody>
                @forelse($students as $student)
                    <tr>
                        <td><strong>{{ $student->name }}</strong></td>
                        <td>{{ $student->email }}</td>
                        <td>{{ $student->identity_number ?? '-' }}</td>
                        <td>{{ $registeredDates[$student->id] }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" style="text-align: center; color: var(--text-muted);">No students registered yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
