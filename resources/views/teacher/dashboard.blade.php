@extends('layouts.app')

@section('title', 'Teacher Dashboard - Open Source Exam Platform')

@section('content')
<div class="container">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <div>
            <h1 style="font-size: 1.8rem; color: var(--primary);">Teacher Dashboard</h1>
            <p style="color: var(--text-muted); font-size: 0.95rem;">Manage question banks, edit exam settings, and publish active tests.</p>
        </div>
        <button class="btn btn-primary" onclick="document.getElementById('modalGroup').style.display='flex'">
            <i class="fa-solid fa-plus"></i> New Question Group
        </button>
    </div>

    <!-- Quick Stats Grid -->
    <div class="grid-stats">
        <div class="stat-box">
            <span style="font-size: 0.85rem; color: var(--text-muted); font-weight: 600;">Question Groups</span>
            <span class="stat-number">{{ $groupsCount }}</span>
        </div>
        <div class="stat-box">
            <span style="font-size: 0.85rem; color: var(--text-muted); font-weight: 600;">Published Exams</span>
            <span class="stat-number">{{ $examsCount }}</span>
        </div>
    </div>

    <!-- Question Groups Bank List -->
    <div class="card">
        <div class="card-header">
            <span>Your Question Banks</span>
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
                            <div style="display: flex; gap: 0.4rem;">
                                <a href="{{ route('teacher.question-groups.show', $g->id) }}" class="btn btn-secondary" style="padding: 0.3rem 0.6rem; font-size: 0.8rem;">
                                    <i class="fa-solid fa-list-check"></i> Manage Questions
                                </a>
                                <form action="{{ route('teacher.question-groups.destroy', $g->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus bank soal ini beserta semua soal di dalamnya?');" style="display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-secondary" style="padding: 0.3rem 0.6rem; font-size: 0.8rem; color: var(--danger);" title="Hapus Bank Soal">
                                        <i class="fa-solid fa-trash-can"></i> Hapus
                                    </button>
                                </form>
                            </div>
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

    <!-- Published Exams List & Management Table -->
    <div class="card">
        <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
            <span>Daftar Ujian Dipublikasikan (Published Exams)</span>
            <span style="font-size: 0.85rem; color: var(--text-muted); font-weight: normal;">Total: {{ $exams->count() }} Ujian</span>
        </div>
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Judul Ujian</th>
                    <th>Bank Soal</th>
                    <th>Token</th>
                    <th>Durasi</th>
                    <th>{{ __('messages.exam_schedule') }}</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($exams as $index => $ex)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td><strong>{{ $ex->title }}</strong></td>
                        <td>{{ $ex->questionGroup->name ?? 'N/A' }}</td>
                        <td><code style="background: rgba(99, 102, 241, 0.1); color: var(--accent); padding: 0.2rem 0.5rem; border-radius: 0.3rem; font-weight: 700;">{{ $ex->token }}</code></td>
                        <td>{{ $ex->duration_minutes }} Menit</td>
                        <td style="min-width: 220px;">
                            <div><strong>{{ __('messages.exam_starts_at') }}:</strong> {{ $examSchedules[$ex->id]['starts_at_display'] }}</div>
                            <div><strong>{{ __('messages.exam_ends_at') }}:</strong> {{ $examSchedules[$ex->id]['ends_at_display'] }}</div>
                            <small style="color: var(--text-muted);">{{ $schoolTimezone }}</small>
                        </td>
                        <td>
                            @if($ex->is_active)
                                <span style="background: rgba(22, 163, 74, 0.1); color: var(--status-answered); padding: 0.2rem 0.6rem; border-radius: 0.4rem; font-weight: 600; font-size: 0.8rem;">Aktif</span>
                            @else
                                <span style="background: rgba(220, 38, 38, 0.1); color: var(--danger); padding: 0.2rem 0.6rem; border-radius: 0.4rem; font-weight: 600; font-size: 0.8rem;">Nonaktif</span>
                            @endif
                        </td>
                        <td>
                            <div style="display: flex; gap: 0.4rem;">
                                <button class="btn btn-secondary" onclick="openEditExamModal({{ $ex->id }})" style="padding: 0.25rem 0.5rem; font-size: 0.8rem; color: var(--primary);" title="Edit Ujian">
                                    <i class="fa-solid fa-pen-to-square"></i> Edit
                                </button>
                                <form action="{{ route('teacher.exams.destroy', $ex->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus publikasi ujian ini?');" style="display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-secondary" style="padding: 0.25rem 0.5rem; font-size: 0.8rem; color: var(--danger);" title="Hapus Ujian">
                                        <i class="fa-solid fa-trash-can"></i> Hapus
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" style="text-align: center; color: var(--text-muted);">Belum ada ujian yang dipublikasikan.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Publish New Exam Panel -->
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
                <div style="display: flex; gap: 0.5rem;">
                    <input type="text" name="token" id="examTokenInput" class="form-control" placeholder="EXAM26" style="text-transform: uppercase;" required>
                    <button type="button" class="btn btn-secondary" onclick="generateToken()" style="white-space: nowrap;" title="Generate Random Token">
                        <i class="fa-solid fa-wand-magic-sparkles"></i> Generate
                    </button>
                </div>
            </div>
            <div class="form-group">
                <label for="duration_minutes">Duration (Minutes)</label>
                <input type="number" name="duration_minutes" class="form-control" value="{{ old('duration_minutes', 60) }}" min="1" required>
            </div>
            <div style="display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 1rem;">
                <div class="form-group">
                    <label for="starts_at">{{ __('messages.exam_starts_at') }}</label>
                    <input type="datetime-local" name="starts_at" id="starts_at" class="form-control" value="{{ old('starts_at', $defaultExamStartsAt) }}" required>
                </div>
                <div class="form-group">
                    <label for="ends_at">{{ __('messages.exam_ends_at') }}</label>
                    <input type="datetime-local" name="ends_at" id="ends_at" class="form-control" value="{{ old('ends_at', $defaultExamEndsAt) }}" required>
                </div>
            </div>
            <p style="color: var(--text-muted); font-size: 0.82rem; margin: -0.25rem 0 1rem;">
                <i class="fa-solid fa-clock"></i> {{ __('messages.school_time') }}: <strong>{{ $schoolTimezone }}</strong>
            </p>
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

<!-- Modal for Edit Exam -->
<div id="modalEditExam" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.7); z-index: 1000; justify-content: center; align-items: center;">
    <div class="card" style="width: 450px; margin: 10% auto;">
        <div class="card-header">
            <span id="editExamModalTitle">Edit Published Exam</span>
            <button class="btn btn-secondary" onclick="document.getElementById('modalEditExam').style.display='none'" style="padding: 0.2rem 0.5rem;">✕</button>
        </div>
        <form id="editExamForm" method="POST">
            @csrf
            @method('PUT')
            <div class="form-group">
                <label for="edit_title">Judul Ujian</label>
                <input type="text" name="title" id="edit_title" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="edit_token">Token Ujian</label>
                <input type="text" name="token" id="edit_token" class="form-control" style="text-transform: uppercase;" required>
            </div>
            <div class="form-group">
                <label for="edit_duration_minutes">Durasi (Menit)</label>
                <input type="number" name="duration_minutes" id="edit_duration_minutes" class="form-control" min="1" required>
            </div>
            <div class="form-group">
                <label for="edit_starts_at">{{ __('messages.exam_starts_at') }}</label>
                <input type="datetime-local" name="starts_at" id="edit_starts_at" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="edit_ends_at">{{ __('messages.exam_ends_at') }}</label>
                <input type="datetime-local" name="ends_at" id="edit_ends_at" class="form-control" required>
                <small style="display: block; color: var(--text-muted); margin-top: 0.3rem;">{{ __('messages.school_time') }}: {{ $schoolTimezone }}</small>
            </div>
            <div class="form-group">
                <label for="edit_is_active">Status Ujian</label>
                <select name="is_active" id="edit_is_active" class="form-control">
                    <option value="1">Aktif (Peserta Bisa Akses)</option>
                    <option value="0">Nonaktif (Dipause / Ditutup)</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary" style="width: 100%;">Simpan Perubahan Ujian</button>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
const examSchedules = {{ Illuminate\Support\Js::from($examSchedules) }};
const examUpdateUrlTemplate = {{ Illuminate\Support\Js::from(route('teacher.exams.update', ['exam' => '__EXAM_ID__'])) }};

function openEditExamModal(examId) {
    const exam = examSchedules[examId];
    const modal = document.getElementById('modalEditExam');
    const form = document.getElementById('editExamForm');
    const titleInput = document.getElementById('edit_title');
    const tokenInput = document.getElementById('edit_token');
    const durationInput = document.getElementById('edit_duration_minutes');
    const activeSelect = document.getElementById('edit_is_active');
    const startsAtInput = document.getElementById('edit_starts_at');
    const endsAtInput = document.getElementById('edit_ends_at');

    form.action = examUpdateUrlTemplate.replace('__EXAM_ID__', exam.id);
    titleInput.value = exam.title || '';
    tokenInput.value = exam.token || '';
    durationInput.value = exam.duration_minutes || 60;
    activeSelect.value = exam.is_active ? '1' : '0';
    startsAtInput.value = exam.starts_at_local;
    endsAtInput.value = exam.ends_at_local;

    modal.style.display = 'flex';
}

function generateToken() {
    const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    let token = '';
    for (let i = 0; i < 6; i++) {
        token += chars.charAt(Math.floor(Math.random() * chars.length));
    }
    document.getElementById('examTokenInput').value = token;
}
</script>
@endsection
