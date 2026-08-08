@extends('layouts.app')

@section('title', 'Manage Questions')

@section('content')
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
    <div>
        <h1 style="font-size: 1.6rem;">Question Group: {{ $group->name }}</h1>
        <p style="color: var(--text-muted);">Add and manage individual questions for this bank.</p>
    </div>
    <a href="{{ route('teacher.dashboard') }}" class="btn btn-secondary">← Back to Dashboard</a>
</div>

<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
    <div class="card">
        <div class="card-header">Add Question</div>
        <form action="{{ route('teacher.questions.store', $group->id) }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="question_type">Question Type</label>
                <select name="question_type" id="question_type" class="form-control" onchange="toggleOptionFields(this.value)">
                    <option value="single_choice">Single Choice (Pilihan Ganda)</option>
                    <option value="multiple_choice">Multiple Choice (Pilihan Kompleks)</option>
                    <option value="true_false">True / False (Benar / Salah)</option>
                    <option value="essay">Essay / Short Answer</option>
                </select>
            </div>
            <div class="form-group">
                <label for="content">Question Content</label>
                <textarea name="content" class="form-control" rows="4" placeholder="Enter question description..." required></textarea>
            </div>

            <div id="optionsContainer">
                <div class="form-group">
                    <label>Option A</label>
                    <input type="text" name="option_a" class="form-control">
                </div>
                <div class="form-group">
                    <label>Option B</label>
                    <input type="text" name="option_b" class="form-control">
                </div>
                <div class="form-group">
                    <label>Option C</label>
                    <input type="text" name="option_c" class="form-control">
                </div>
                <div class="form-group">
                    <label>Option D</label>
                    <input type="text" name="option_d" class="form-control">
                </div>
            </div>

            <div class="form-group">
                <label for="correct_answer">Correct Answer Key / Text</label>
                <input type="text" name="correct_answer" class="form-control" placeholder="e.g. A or true or correct answer text" required>
            </div>

            <div class="form-group">
                <label for="explanation">Explanation (Pembahasan)</label>
                <textarea name="explanation" class="form-control" rows="2" placeholder="Optional answer explanation..."></textarea>
            </div>

            <div class="form-group">
                <label for="weight">Weight / Score</label>
                <input type="number" name="weight" class="form-control" value="1" min="1" required>
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%;">Save Question</button>
        </form>
    </div>

    <div class="card">
        <div class="card-header">Questions List ({{ $group->questions->count() }})</div>
        <div style="display: flex; flex-direction: column; gap: 1rem;">
            @forelse($group->questions as $index => $q)
                <div style="background: var(--bg-body); padding: 1rem; border-radius: 0.5rem; border: 1px solid var(--border-color);">
                    <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                        <span style="font-weight: 600; color: var(--primary);">#{{ $index + 1 }} ({{ strtoupper($q->question_type) }})</span>
                        <span style="color: var(--accent); font-size: 0.85rem;">Weight: {{ $q->weight }}</span>
                    </div>
                    <p style="margin-bottom: 0.5rem;">{{ $q->content }}</p>
                    @if($q->options_json)
                        <ul style="list-style: none; padding-left: 1rem; color: var(--text-muted); font-size: 0.9rem;">
                            @foreach($q->options_json as $opt)
                                <li>{{ $opt['id'] }}. {{ $opt['text'] }}</li>
                            @endforeach
                        </ul>
                    @endif
                    <div style="margin-top: 0.5rem; font-size: 0.85rem; color: var(--accent);">
                        <strong>Correct Answer:</strong> {{ is_array($q->correct_answers_json) ? implode(', ', $q->correct_answers_json) : $q->correct_answers_json }}
                    </div>
                    @if($q->explanation)
                        <div style="margin-top: 0.25rem; font-size: 0.8rem; color: var(--text-muted);">
                            <em>Explanation: {{ $q->explanation }}</em>
                        </div>
                    @endif
                </div>
            @empty
                <p style="text-align: center; color: var(--text-muted);">No questions added to this group yet.</p>
            @endforelse
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
function toggleOptionFields(type) {
    const container = document.getElementById('optionsContainer');
    if (type === 'single_choice' || type === 'multiple_choice') {
        container.style.display = 'block';
    } else {
        container.style.display = 'none';
    }
}
</script>
@endsection
