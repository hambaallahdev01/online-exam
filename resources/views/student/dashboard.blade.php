@extends('layouts.app')

@section('title', 'Student Portal')

@section('content')
<div style="margin-bottom: 2rem;">
    <h1 style="font-size: 1.8rem;">Welcome, {{ Auth::user()->name }}</h1>
    <p style="color: var(--text-muted);">Enter token to join an ongoing exam or view your exam history.</p>
</div>

<div style="display: grid; grid-template-columns: 1fr 2fr; gap: 1.5rem;">
    <!-- Enter Exam Token Card -->
    <div class="card" style="border: 1px solid var(--primary);">
        <div class="card-header" style="color: var(--primary);">
            <i class="fa-solid fa-key"></i> Enter Exam Token
        </div>
        <form action="{{ route('student.enter-token') }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="token">6-Character Exam Token</label>
                <input type="text" id="token" name="token" class="form-control" placeholder="e.g. EXAM26" style="font-size: 1.2rem; text-align: center; text-transform: uppercase; letter-spacing: 2px;" required>
            </div>
            <button type="submit" class="btn btn-primary" style="width: 100%; padding: 0.75rem;">Start Exam Session</button>
        </form>
    </div>

    <!-- Exam History & Results Card -->
    <div class="card">
        <div class="card-header">
            <i class="fa-solid fa-chart-column"></i> My Exam Results
        </div>
        <table>
            <thead>
                <tr>
                    <th>Exam Title</th>
                    <th>Date</th>
                    <th>Status</th>
                    <th>Score</th>
                </tr>
            </thead>
            <tbody>
                @forelse($myResults as $res)
                    <tr>
                        <td><strong>{{ $res->exam->title }}</strong></td>
                        <td>{{ $resultDates[$res->id] }} <small style="color: var(--text-muted);">({{ $schoolTimezone }})</small></td>
                        <td>
                            @if($res->status === 'graded' || $res->status === 'submitted')
                                <span style="background: rgba(16, 185, 129, 0.2); color: #34d399; padding: 0.2rem 0.5rem; border-radius: 0.25rem; font-size: 0.8rem;">Submitted</span>
                            @else
                                <span style="background: rgba(245, 158, 11, 0.2); color: #fbbf24; padding: 0.2rem 0.5rem; border-radius: 0.25rem; font-size: 0.8rem;">In Progress</span>
                            @endif
                        </td>
                        <td>
                            <strong style="font-size: 1.1rem; color: var(--accent);">{{ $res->score }}%</strong>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" style="text-align: center; color: var(--text-muted);">No exam history yet. Enter token above to begin your first exam.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
