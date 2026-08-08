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
                    <option value="single_choice">1. Pilihan Ganda (Single Choice)</option>
                    <option value="multiple_choice">2. Pilihan Banyak (Multiple Choice)</option>
                    <option value="essay">3. Uraian (Essay)</option>
                    <option value="true_false">4. Benar - Salah (True / False)</option>
                    <option value="fact_opinion">5. Fakta - Opini (Fact / Opinion)</option>
                    <option value="matching">6. Mencocokkan (Matching)</option>
                    <option value="sorting">7. Mengurutkan (Sorting)</option>
                </select>
            </div>
            <div class="form-group">
                <label for="content">Question Content / Instructions</label>
                <textarea name="content" class="form-control" rows="4" placeholder="Enter question text or instructions..." required></textarea>
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
                <label for="correct_answer" id="correctAnswerLabel">Correct Answer Key / Text</label>
                <input type="text" name="correct_answer" id="correct_answer" class="form-control" placeholder="e.g. B (or fact / opinion / A,C or json pairs / ordered items)" required>
                <small style="color: var(--text-muted); display: block; margin-top: 0.25rem;" id="correctAnswerHint">
                    For Single Choice: A/B/C/D. For Multiple: A,B,C. For Fact/Opinion: fact or opinion. For Sorting: Item1,Item2,Item3.
                </small>
            </div>

            <div class="form-group">
                <label for="explanation">Explanation (Pembahasan)</label>
                <textarea name="explanation" class="form-control" rows="2" placeholder="Optional answer explanation..."></textarea>
            </div>

            <div class="form-group">
                <label for="weight">Weight / Score</label>
                <input type="number" name="weight" class="form-control" value="10" min="1" required>
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
                        <span style="font-weight: 600; color: var(--primary);">#{{ $index + 1 }} ({{ strtoupper(str_replace('_', ' ', $q->question_type)) }})</span>
                        <span style="color: var(--accent); font-size: 0.85rem;">Weight: {{ $q->weight }}</span>
                    </div>
                    <p style="margin-bottom: 0.5rem; font-weight: 500;">{{ $q->content }}</p>

                    @if($q->options_json)
                        @if($q->question_type === 'matching')
                            <div style="font-size: 0.85rem; color: var(--text-muted); margin-bottom: 0.5rem;">
                                Pairs: {{ json_encode($q->options_json) }}
                            </div>
                        @elseif(is_array($q->options_json))
                            <ul style="list-style: none; padding-left: 1rem; color: var(--text-muted); font-size: 0.9rem;">
                                @foreach($q->options_json as $opt)
                                    @if(is_array($opt))
                                        <li>{{ $opt['id'] ?? '' }}. {{ $opt['text'] ?? '' }}</li>
                                    @else
                                        <li>• {{ $opt }}</li>
                                    @endif
                                @endforeach
                            </ul>
                        @endif
                    @endif

                    <div style="margin-top: 0.5rem; font-size: 0.85rem; color: var(--status-answered);">
                        <strong>Correct Answer:</strong> {{ is_array($q->correct_answers_json) ? json_encode($q->correct_answers_json) : $q->correct_answers_json }}
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
    const hint = document.getElementById('correctAnswerHint');
    
    if (type === 'single_choice' || type === 'multiple_choice') {
        container.style.display = 'block';
        hint.textContent = 'For Single Choice: A/B/C/D. For Multiple Choice: A,B,C';
    } else {
        container.style.display = 'none';
        if (type === 'fact_opinion') {
            hint.textContent = 'Enter: "fact" or "opinion"';
        } else if (type === 'true_false') {
            hint.textContent = 'Enter: "true" or "false"';
        } else if (type === 'sorting') {
            hint.textContent = 'Enter items in correct order separated by comma, e.g.: First Step, Second Step, Third Step';
        } else if (type === 'matching') {
            hint.textContent = 'Enter JSON pairs e.g.: {"Indonesia":"Jakarta","Japan":"Tokyo"}';
        } else {
            hint.textContent = 'Enter correct answer text';
        }
    }
}
</script>
@endsection
