@extends('layouts.app')

@section('title', 'Manage Teachers')

@section('content')
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
    <div>
        <h1 style="font-size: 1.6rem;">Teacher Management</h1>
        <p style="color: var(--text-muted);">Register teachers for your school.</p>
    </div>
</div>

<div style="display: grid; grid-template-columns: 1fr 2fr; gap: 1.5rem;">
    <div class="card">
        <div class="card-header">Register Teacher</div>
        <form action="{{ route('admin.teachers') }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="name">Full Name</label>
                <input type="text" name="name" class="form-control" placeholder="Dr. Jane Smith" required>
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" name="email" class="form-control" placeholder="teacher@school.org" required>
            </div>
            <div class="form-group">
                <label for="identity_number">NIP / Identity No</label>
                <input type="text" name="identity_number" class="form-control" placeholder="NIP1985...">
            </div>
            <div class="form-group">
                <label for="password">Default Password</label>
                <input type="password" name="password" class="form-control" placeholder="••••••••" required>
            </div>
            <button type="submit" class="btn btn-primary" style="width: 100%;">Add Teacher</button>
        </form>
    </div>

    <div class="card">
        <div class="card-header">Registered Teachers</div>
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>NIP / Identity</th>
                    <th>Joined</th>
                </tr>
            </thead>
            <tbody>
                @forelse($teachers as $teacher)
                    <tr>
                        <td><strong>{{ $teacher->name }}</strong></td>
                        <td>{{ $teacher->email }}</td>
                        <td>{{ $teacher->identity_number ?? '-' }}</td>
                        <td>{{ $registeredDates[$teacher->id] }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" style="text-align: center; color: var(--text-muted);">No teachers registered yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
