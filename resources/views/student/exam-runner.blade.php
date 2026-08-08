@extends('layouts.app')

@section('title', 'Exam Session - ' . $exam->title)

@section('content')
<!-- Header Panel with Timer and Status -->
<div style="display: flex; justify-content: space-between; align-items: center; background: var(--bg-card); padding: 1rem 1.5rem; border-radius: 0.75rem; border: 1px solid var(--border-color); margin-bottom: 1.5rem;">
    <div>
        <h2 style="font-size: 1.25rem;">{{ $exam->title }}</h2>
        <span style="color: var(--text-muted); font-size: 0.85rem;" id="saveStatus">Status: Connected (Autosave active)</span>
    </div>

    <div style="display: flex; align-items: center; gap: 1.25rem;">
        <div style="text-align: right;">
            <div style="font-size: 0.75rem; color: var(--text-muted); text-transform: uppercase; font-weight: 600;">Time Remaining</div>
            <div id="timerDisplay" style="font-size: 1.5rem; font-weight: 700; color: var(--status-timer); font-family: monospace;">
                00:00:00
            </div>
        </div>
        <button type="button" class="btn btn-primary" style="background-color: var(--primary);" id="submitExamBtn" onclick="confirmSubmitExam()">Finish & Submit</button>
    </div>
</div>

<div style="display: grid; grid-template-columns: 3fr 1fr; gap: 1.5rem;">
    <!-- Main Question View -->
    <div class="card" id="questionCard" style="min-height: 420px; display: flex; flex-direction: column; justify-content: space-between;">
        <div id="loadingBox" style="text-align: center; padding: 4rem 1rem; color: var(--text-muted);">
            ⏳ Loading exam questions and restoring session state...
        </div>

        <div id="questionContainer" style="display: none;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem; border-bottom: 1px solid var(--border-color); padding-bottom: 0.75rem;">
                <span id="questionHeader" style="font-size: 1.1rem; font-weight: 700; color: var(--primary);">Question 1 of 10</span>
                
                <div style="display: flex; align-items: center; gap: 1rem;">
                    <label style="display: inline-flex; align-items: center; gap: 0.4rem; font-size: 0.9rem; color: var(--warning); cursor: pointer; font-weight: 600; background: rgba(245, 158, 11, 0.1); padding: 0.3rem 0.6rem; border-radius: 0.4rem;">
                        <input type="checkbox" id="flagQuestionCheckbox" onchange="toggleFlagCurrentQuestion(this.checked)">
                        <span>🚩 Ragu-ragu</span>
                    </label>

                    <span id="questionTypeBadge" style="background: rgba(99, 102, 241, 0.15); color: var(--primary); padding: 0.25rem 0.6rem; border-radius: 0.35rem; font-size: 0.8rem; font-weight: 600;">Single Choice</span>
                </div>
            </div>

            <!-- Question Content with Focus-Friendly Formatting -->
            <div id="questionText" style="font-size: 1.15rem; margin-bottom: 1.75rem; line-height: 1.8; color: var(--text-main); font-weight: 500;"></div>

            <div id="optionsBox" style="display: flex; flex-direction: column; gap: 0.85rem; margin-bottom: 2rem;"></div>

            <div id="essayBox" style="display: none; margin-bottom: 2rem;">
                <textarea id="essayAnswerInput" class="form-control" rows="6" placeholder="Type your answer here..." oninput="handleAnswerChange()" style="line-height: 1.7; font-size: 1rem;"></textarea>
            </div>
        </div>

        <div id="navigationBox" style="display: none; justify-content: space-between; align-items: center; border-top: 1px solid var(--border-color); padding-top: 1rem;">
            <button type="button" class="btn btn-secondary" id="prevBtn" onclick="navigateQuestion(-1)">← Previous</button>
            <button type="button" class="btn btn-primary" id="nextBtn" onclick="navigateQuestion(1)">Next →</button>
        </div>
    </div>

    <!-- Question Palette Drawer -->
    <div class="card">
        <div class="card-header" style="font-size: 1rem;">Question Palette</div>
        
        <!-- Status Color Legend -->
        <div style="font-size: 0.75rem; display: flex; flex-direction: column; gap: 0.3rem; margin-bottom: 1rem; color: var(--text-muted);">
            <div style="display: flex; align-items: center; gap: 0.5rem;">
                <span style="width: 12px; height: 12px; border-radius: 3px; background: var(--status-answered); display: inline-block;"></span>
                <span>Sudah Dijawab</span>
            </div>
            <div style="display: flex; align-items: center; gap: 0.5rem;">
                <span style="width: 12px; height: 12px; border-radius: 3px; background: var(--status-unanswered); border: 1px solid var(--border-color); display: inline-block;"></span>
                <span>Belum Dijawab</span>
            </div>
            <div style="display: flex; align-items: center; gap: 0.5rem;">
                <span style="width: 12px; height: 12px; border-radius: 3px; background: var(--status-flagged); display: inline-block;"></span>
                <span>Ragu-ragu</span>
            </div>
            <div style="display: flex; align-items: center; gap: 0.5rem;">
                <span style="width: 12px; height: 12px; border-radius: 3px; border: 2px solid var(--status-active); display: inline-block;"></span>
                <span>Sedang Dibuka</span>
            </div>
        </div>

        <div id="paletteGrid" style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 0.5rem;"></div>
    </div>
</div>
@endsection

@section('scripts')
<script>
const EXAM_ID = {{ $exam->id }};
const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

let questions = [];
let currentIndex = 0;
let userAnswers = {};
let flaggedQuestions = {};
let timeRemaining = 0;
let timerInterval = null;
let autosaveInterval = null;

document.addEventListener('DOMContentLoaded', () => {
    fetchExamPayload();
});

async function fetchExamPayload() {
    try {
        const response = await fetch(`/student/api/exam/${EXAM_ID}/payload`, {
            headers: { 'Accept': 'application/json' }
        });
        const data = await response.json();

        if (data.status === 'success') {
            questions = data.questions;
            userAnswers = data.result.answers || {};
            timeRemaining = data.result.time_remaining_seconds;

            document.getElementById('loadingBox').style.display = 'none';
            document.getElementById('questionContainer').style.display = 'block';
            document.getElementById('navigationBox').style.display = 'flex';

            renderPalette();
            renderQuestion(0);
            startTimer();

            // Autosave every 15 seconds
            autosaveInterval = setInterval(autosaveAnswers, 15000);
        } else {
            alert('Failed to load exam data.');
        }
    } catch (err) {
        console.error('Payload fetch error:', err);
        document.getElementById('loadingBox').innerHTML = '❌ Connection error loading questions. Please refresh the page.';
    }
}

function startTimer() {
    updateTimerDisplay();
    timerInterval = setInterval(() => {
        if (timeRemaining > 0) {
            timeRemaining--;
            updateTimerDisplay();
        } else {
            clearInterval(timerInterval);
            alert('Time expired! Submitting exam automatically...');
            submitExam(true);
        }
    }, 1000);
}

function updateTimerDisplay() {
    const hours = Math.floor(timeRemaining / 3600);
    const minutes = Math.floor((timeRemaining % 3600) / 60);
    const seconds = timeRemaining % 60;

    const pad = (n) => n.toString().padStart(2, '0');
    document.getElementById('timerDisplay').textContent = `${pad(hours)}:${pad(minutes)}:${pad(seconds)}`;
}

function renderQuestion(index) {
    if (index < 0 || index >= questions.length) return;
    currentIndex = index;

    const q = questions[currentIndex];
    document.getElementById('questionHeader').textContent = `Question ${currentIndex + 1} of ${questions.length}`;
    document.getElementById('questionTypeBadge').textContent = q.type.replace('_', ' ').toUpperCase();
    document.getElementById('questionText').textContent = q.content;
    document.getElementById('flagQuestionCheckbox').checked = !!flaggedQuestions[q.id];

    const optionsBox = document.getElementById('optionsBox');
    const essayBox = document.getElementById('essayBox');

    optionsBox.innerHTML = '';
    optionsBox.style.display = 'none';
    essayBox.style.display = 'none';

    if (q.type === 'single_choice' || q.type === 'true_false') {
        optionsBox.style.display = 'flex';
        const currentAns = userAnswers[q.id];

        (q.options || []).forEach(opt => {
            const isChecked = currentAns === opt.id;
            const label = document.createElement('label');
            label.style.cssText = `
                display: flex; align-items: center; gap: 0.85rem; padding: 0.95rem 1.25rem;
                background: ${isChecked ? 'rgba(37, 99, 235, 0.08)' : 'var(--bg-card)'};
                border: 2px solid ${isChecked ? 'var(--status-active)' : 'var(--border-color)'};
                border-radius: 0.5rem; cursor: pointer; transition: all 0.2s ease;
                color: var(--text-main); font-size: 1rem;
            `;

            label.innerHTML = `
                <input type="radio" name="opt_${q.id}" value="${opt.id}" ${isChecked ? 'checked' : ''} onchange="handleSingleSelect('${q.id}', '${opt.id}')">
                <span><strong>${opt.id}.</strong> ${opt.text}</span>
            `;
            optionsBox.appendChild(label);
        });

    } else if (q.type === 'multiple_choice') {
        optionsBox.style.display = 'flex';
        const currentAnsList = Array.isArray(userAnswers[q.id]) ? userAnswers[q.id] : [];

        (q.options || []).forEach(opt => {
            const isChecked = currentAnsList.includes(opt.id);
            const label = document.createElement('label');
            label.style.cssText = `
                display: flex; align-items: center; gap: 0.85rem; padding: 0.95rem 1.25rem;
                background: ${isChecked ? 'rgba(37, 99, 235, 0.08)' : 'var(--bg-card)'};
                border: 2px solid ${isChecked ? 'var(--status-active)' : 'var(--border-color)'};
                border-radius: 0.5rem; cursor: pointer; transition: all 0.2s ease;
                color: var(--text-main); font-size: 1rem;
            `;

            label.innerHTML = `
                <input type="checkbox" name="opt_${q.id}" value="${opt.id}" ${isChecked ? 'checked' : ''} onchange="handleMultipleSelect('${q.id}', '${opt.id}', this.checked)">
                <span><strong>${opt.id}.</strong> ${opt.text}</span>
            `;
            optionsBox.appendChild(label);
        });

    } else if (q.type === 'essay') {
        essayBox.style.display = 'block';
        document.getElementById('essayAnswerInput').value = userAnswers[q.id] || '';
    }

    document.getElementById('prevBtn').disabled = (currentIndex === 0);
    document.getElementById('nextBtn').disabled = (currentIndex === questions.length - 1);

    renderPalette();
}

function toggleFlagCurrentQuestion(isFlagged) {
    const q = questions[currentIndex];
    if (isFlagged) {
        flaggedQuestions[q.id] = true;
    } else {
        delete flaggedQuestions[q.id];
    }
    renderPalette();
}

function handleSingleSelect(questionId, val) {
    userAnswers[questionId] = val;
    renderQuestion(currentIndex);
    triggerAutosaveNotification();
}

function handleMultipleSelect(questionId, val, isChecked) {
    let list = Array.isArray(userAnswers[questionId]) ? [...userAnswers[questionId]] : [];
    if (isChecked) {
        if (!list.includes(val)) list.push(val);
    } else {
        list = list.filter(item => item !== val);
    }
    userAnswers[questionId] = list;
    renderQuestion(currentIndex);
    triggerAutosaveNotification();
}

function handleAnswerChange() {
    const q = questions[currentIndex];
    const val = document.getElementById('essayAnswerInput').value;
    userAnswers[q.id] = val;
    renderPalette();
    triggerAutosaveNotification();
}

function navigateQuestion(direction) {
    renderQuestion(currentIndex + direction);
}

function renderPalette() {
    const grid = document.getElementById('paletteGrid');
    grid.innerHTML = '';

    questions.forEach((q, idx) => {
        const hasAnswer = userAnswers[q.id] && (
            (typeof userAnswers[q.id] === 'string' && userAnswers[q.id].trim() !== '') ||
            (Array.isArray(userAnswers[q.id]) && userAnswers[q.id].length > 0)
        );

        const isFlagged = !!flaggedQuestions[q.id];
        const isActive = (idx === currentIndex);

        let bgColor = 'var(--status-unanswered)';
        let textColor = 'var(--text-main)';

        if (isFlagged) {
            bgColor = 'var(--status-flagged)';
            textColor = '#FFFFFF';
        } else if (hasAnswer) {
            bgColor = 'var(--status-answered)';
            textColor = '#FFFFFF';
        }

        const btn = document.createElement('button');
        btn.type = 'button';
        btn.style.cssText = `
            padding: 0.65rem 0.4rem; font-weight: 700; border-radius: 0.4rem; cursor: pointer;
            background: ${bgColor}; color: ${textColor}; font-size: 0.9rem;
            border: ${isActive ? '3px solid var(--status-active)' : '1px solid var(--border-color)'};
            box-shadow: ${isActive ? '0 0 0 2px rgba(37, 99, 235, 0.3)' : 'none'};
            transition: all 0.15s ease;
        `;
        btn.textContent = idx + 1;
        btn.onclick = () => renderQuestion(idx);
        grid.appendChild(btn);
    });
}

function triggerAutosaveNotification() {
    document.getElementById('saveStatus').textContent = 'Status: Saving changes...';
    document.getElementById('saveStatus').style.color = 'var(--warning)';
    autosaveAnswers();
}

async function autosaveAnswers() {
    try {
        const response = await fetch(`/student/api/exam/${EXAM_ID}/autosave`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': CSRF_TOKEN,
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                answers: userAnswers,
                time_remaining_seconds: timeRemaining
            })
        });
        const res = await response.json();
        if (res.status === 'success') {
            document.getElementById('saveStatus').textContent = 'Status: Autosaved successfully';
            document.getElementById('saveStatus').style.color = 'var(--status-answered)';
        }
    } catch (e) {
        document.getElementById('saveStatus').textContent = 'Status: Offline mode (saving locally)';
        document.getElementById('saveStatus').style.color = 'var(--danger)';
    }
}

function confirmSubmitExam() {
    if (confirm('Are you sure you want to finish and submit your exam? You cannot change your answers after submitting.')) {
        submitExam(false);
    }
}

async function submitExam(isAutoSubmit = false) {
    clearInterval(timerInterval);
    clearInterval(autosaveInterval);

    try {
        const response = await fetch(`/student/api/exam/${EXAM_ID}/submit`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': CSRF_TOKEN,
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                answers: userAnswers
            })
        });
        const res = await response.json();

        if (res.status === 'success' || res.status === 'already_submitted') {
            alert(`Exam submitted successfully! Score: ${res.score ?? 'Processed'}`);
            window.location.href = res.redirect_url || '/student/dashboard';
        }
    } catch (e) {
        alert('Failed to submit exam. Please check your internet connection.');
    }
}
</script>
@endsection
