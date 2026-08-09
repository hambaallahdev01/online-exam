@extends('layouts.app')

@section('title', 'Teacher Dashboard')

@section('content')
<div style="margin-bottom: 2rem;">
    <h1 style="font-size: 1.8rem;">Teacher Workspace</h1>
    <p style="color: var(--text-muted);">Manage Question Banks & Schedule Online Exams</p>
</div>

<div class="grid-stats">
    <div class="stat-box">
        <div style="color: var(--text-muted); font-size: 0.9rem;">Question Groups</div>
        <div class="stat-number">{{ $groupsCount }}</div>
    </div>
    <div class="stat-box">
        <div style="color: var(--text-muted); font-size: 0.9rem;">Published Exams</div>
        <div class="stat-number" style="color: var(--accent);">{{ $examsCount }}</div>
    </div>
</div>

<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
    <!-- Question Groups Panel -->
    <div class="card">
        <div class="card-header">
            <span>Question Groups</span>
            <button class="btn btn-primary" onclick="document.getElementById('modalGroup').style.display='block'" style="padding: 0.4rem 0.8rem; font-size: 0.85rem;">+ New Group</button>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Group Name</th>
                    <th>Questions</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($questionGroups as $g)
                    <tr>
                        <td><strong>{{ $g->name }}</strong></td>
                        <td>{{ $g->questions_count }} questions</td>
                        <td>
                            <a href="{{ route('teacher.question-groups.show', $g->id) }}" class="btn btn-secondary" style="padding: 0.3rem 0.6rem; font-size: 0.8rem;">Manage Questions</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" style="text-align: center; color: var(--text-muted);">No question groups created yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Active Exams Panel -->
    <div class="card">
        <div class="card-header">
            <span>Publish New Exam</span>
        </div>
        <form action="{{ route('teacher.exams.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="question_group_id">Select Question Bank</label>
                <select name="question_group_id" id="question_group_id" class="form-control" required>
                    @foreach($questionGroups as $group)
                        <option value="{{ $group->id }}">{{ $group->name }} ({{ $group->questions_count }} questions)</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label for="title">Exam Title</label>
                <input type="text" name="title" class="form-control" placeholder="Midterm CS101" required>
            </div>
            <div class="form-group">
                <label for="token">Exam Token (6 chars)</label>
                <input type="text" name="token" class="form-control" placeholder="EXAM26" style="text-transform: uppercase;" required>
            </div>
            <div class="form-group">
                <label for="duration_minutes">Duration (Minutes)</label>
                <input type="number" name="duration_minutes" class="form-control" value="60" min="5" required>
            </div>
            <button type="submit" class="btn btn-accent" style="width: 100%;">Publish Exam</button>
        </form>
    </div>
</div>

<!-- Modal for New Group -->
<div id="modalGroup" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.7); z-index: 1000; justify-content: center; align-items: center;">
    <div class="card" style="width: 450px; margin: 10% auto;">
        <div class="card-header">
            <span>Create Question Group</span>
            <button class="btn btn-secondary" onclick="document.getElementById('modalGroup').style.display='none'" style="padding: 0.2rem 0.5rem;">✕</button>
        </div>
        <form action="{{ route('teacher.question-groups.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="name">Group Name</label>
                <input type="text" name="name" class="form-control" placeholder="Web Development Basics" required>
            </div>
            @if($subjects->count() > 0)
                <div class="form-group">
                    <label for="subject_id">Subject / Mata Pelajaran</label>
                    <select name="subject_id" id="subject_id" class="form-control">
                        @foreach($subjects as $subj)
                            <option value="{{ $subj->id }}">{{ $subj->name }} ({{ $subj->code }})</option>
                        @endforeach
                    </select>
                </div>
            @endif
            <div class="form-group">
                <label for="description">Description</label>
                <textarea name="description" class="form-control" rows="3" placeholder="Description or target chapter..."></textarea>
            </div>
            <button type="submit" class="btn btn-primary" style="width: 100%;">Create</button>
        </form>
    </div>
</div>
@endsection
