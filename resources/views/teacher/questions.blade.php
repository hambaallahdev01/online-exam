@extends('layouts.app')

@section('title', 'Manage Questions - ' . $group->name)

@section('styles')
<style>
    .editor-toolbar {
        display: flex;
        flex-wrap: wrap;
        gap: 0.35rem;
        padding: 0.5rem;
        background: var(--bg-card-hover);
        border: 1px solid var(--border-color);
        border-radius: 0.5rem 0.5rem 0 0;
        border-bottom: none;
    }
    .editor-btn {
        background: var(--bg-card);
        color: var(--text-main);
        border: 1px solid var(--border-color);
        border-radius: 0.35rem;
        padding: 0.35rem 0.65rem;
        font-size: 0.85rem;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        transition: background 0.15s ease, border-color 0.15s ease;
    }
    .editor-btn:hover {
        background: var(--primary);
        color: #ffffff;
        border-color: var(--primary);
    }
    .editor-select {
        background: var(--bg-card);
        color: var(--text-main);
        border: 1px solid var(--border-color);
        border-radius: 0.35rem;
        padding: 0.3rem 0.5rem;
        font-size: 0.85rem;
    }
    .editor-content {
        min-height: 180px;
        max-height: 400px;
        overflow-y: auto;
        padding: 0.85rem;
        background: var(--bg-card);
        color: var(--text-main);
        border: 1px solid var(--border-color);
        border-radius: 0 0 0.5rem 0.5rem;
        outline: none;
        line-height: 1.6;
        font-size: 1rem;
        position: relative;
    }
    .option-editor-content {
        min-height: 70px;
        max-height: 200px;
        overflow-y: auto;
        padding: 0.6rem;
        background: var(--bg-card);
        color: var(--text-main);
        border: 1px solid var(--border-color);
        border-radius: 0 0 0.5rem 0.5rem;
        outline: none;
        font-size: 0.95rem;
    }
    .editor-content:focus, .option-editor-content:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 2px rgba(99, 102, 241, 0.2);
    }
    .editor-content img, .option-editor-content img {
        max-width: 100%;
        height: auto;
        border-radius: 0.5rem;
        cursor: pointer;
        transition: outline 0.15s ease, box-shadow 0.15s ease;
    }
    .editor-content img.selected-editor-img, .option-editor-content img.selected-editor-img {
        outline: 3px solid var(--accent);
        box-shadow: 0 0 12px rgba(99, 102, 241, 0.4);
    }
    .image-resizer-popover {
        display: none;
        position: absolute;
        background: var(--bg-card);
        border: 1px solid var(--border-color);
        padding: 0.35rem 0.6rem;
        border-radius: 0.4rem;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        z-index: 1000;
        gap: 0.35rem;
        align-items: center;
    }
    .pdf-attachment-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.5rem 0.85rem;
        background: rgba(220, 38, 38, 0.1);
        border: 1px solid var(--danger);
        color: var(--danger);
        border-radius: 0.4rem;
        text-decoration: none;
        font-weight: 600;
        font-size: 0.9rem;
        margin: 0.5rem 0;
    }
    .video-responsive-container {
        position: relative;
        padding-bottom: 56.25%;
        height: 0;
        overflow: hidden;
        max-width: 100%;
        margin: 1rem 0;
        border-radius: 0.5rem;
    }
    .video-responsive-container iframe {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        border: 0;
    }
</style>
@endsection

@section('content')
<div class="container">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
        <div>
            <h1 style="font-size: 1.6rem; color: var(--primary);">Bank Soal: {{ $group->name }}</h1>
            <p style="color: var(--text-muted); font-size: 0.9rem;">Mata Pelajaran: {{ $group->subject->name }} | Dibuat oleh: {{ $group->teacher->name }}</p>
        </div>
        <a href="{{ route('teacher.dashboard') }}" class="btn btn-secondary"><i class="fa-solid fa-arrow-left"></i> Kembali ke Dashboard</a>
    </div>

    <!-- Upload Progress Loading Overlay Modal -->
    <div id="uploadLoadingOverlay" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(15, 23, 42, 0.6); backdrop-filter: blur(4px); z-index: 9999; align-items: center; justify-content: center; flex-direction: column;">
        <div style="background: var(--bg-card); padding: 2rem 2.5rem; border-radius: 1rem; box-shadow: 0 10px 30px rgba(0,0,0,0.25); border: 1px solid var(--border-color); text-align: center; display: flex; flex-direction: column; align-items: center; gap: 0.85rem; max-width: 90%;">
            <i class="fa-solid fa-circle-notch fa-spin" style="font-size: 2.8rem; color: var(--accent);"></i>
            <h3 style="font-size: 1.15rem; font-weight: 700; color: var(--primary); margin: 0;">Mengunggah Berkas Media...</h3>
            <p style="font-size: 0.85rem; color: var(--text-muted); margin: 0;" id="uploadLoadingText">Sedang mengompresi & menyimpan ke S3 Storage. Mohon tunggu sejenak.</p>
        </div>
    </div>

    <div class="card" id="formCard">
        <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
            <span id="formCardHeader">Tambah Soal Baru</span>
            <button type="button" class="btn btn-secondary" id="cancelEditBtn" onclick="cancelEdit()" style="display: none; padding: 0.25rem 0.65rem; font-size: 0.8rem;">
                <i class="fa-solid fa-xmark"></i> Batal Edit
            </button>
        </div>
        <form action="{{ route('teacher.questions.store', $group->id) }}" method="POST" id="createQuestionForm">
            @csrf
            <div id="formMethodContainer"></div>

            <div class="form-group">
                <label for="question_type">Tipe Soal</label>
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

            <!-- Native Lightweight WYSIWYG Editor for Question Content -->
            <div class="form-group">
                <label for="editorContent">Isi Pertanyaan / Instruksi Soal (WYSIWYG Rich Editor)</label>
                
                <div class="editor-toolbar">
                    <select class="editor-select" onchange="execCmd('formatBlock', this.value); this.selectedIndex=0;">
                        <option value="" disabled selected>Format Paragraf</option>
                        <option value="p">Paragraf Standar</option>
                        <option value="h3">Judul Utama (H3)</option>
                        <option value="h4">Sub Judul (H4)</option>
                        <option value="pre">Blok Kode / Teks Tetap</option>
                    </select>

                    <button type="button" class="editor-btn" onclick="execCmd('bold')" title="Tebal (Bold)"><i class="fa-solid fa-bold"></i></button>
                    <button type="button" class="editor-btn" onclick="execCmd('italic')" title="Miring (Italic)"><i class="fa-solid fa-italic"></i></button>
                    <button type="button" class="editor-btn" onclick="execCmd('underline')" title="Garis Bawah (Underline)"><i class="fa-solid fa-underline"></i></button>
                    <button type="button" class="editor-btn" onclick="execCmd('strikeThrough')" title="Coret (Strikethrough)"><i class="fa-solid fa-strikethrough"></i></button>
                    
                    <button type="button" class="editor-btn" onclick="execCmd('insertUnorderedList')" title="Daftar Bullet"><i class="fa-solid fa-list-ul"></i></button>
                    <button type="button" class="editor-btn" onclick="execCmd('insertOrderedList')" title="Daftar Angka"><i class="fa-solid fa-list-ol"></i></button>
                    
                    <button type="button" class="editor-btn" onclick="triggerMediaUpload('image', 'editorContent')" title="Upload & Auto-Resize Gambar (Max 1024x1024)">
                        <i class="fa-solid fa-image" style="color: var(--primary);"></i> Sisipkan Gambar
                    </button>

                    <button type="button" class="editor-btn" onclick="insertYoutubeVideo('editorContent')" title="Embed Video YouTube">
                        <i class="fa-brands fa-youtube" style="color: #ef4444;"></i> Embed YouTube
                    </button>

                    <button type="button" class="editor-btn" onclick="triggerMediaUpload('pdf', 'editorContent')" title="Lampirkan Dokumen PDF (Max 5MB)">
                        <i class="fa-solid fa-file-pdf" style="color: #dc2626;"></i> Dokumen PDF
                    </button>

                    <button type="button" class="editor-btn" onclick="execCmd('removeFormat')" title="Hapus Format"><i class="fa-solid fa-eraser"></i></button>
                </div>

                <div id="editorContent" class="editor-content" contenteditable="true"></div>
                <input type="hidden" name="content" id="hiddenQuestionContent" required>
            </div>

            <!-- Floating Toolbar for Image Resizing & Deletion -->
            <div id="imageResizerPopover" class="image-resizer-popover">
                <span style="font-size: 0.8rem; color: var(--text-muted); margin-right: 0.25rem;">Ukuran Gambar:</span>
                <button type="button" class="editor-btn" style="padding: 0.2rem 0.45rem; font-size: 0.75rem;" onclick="resizeSelectedImg('25%')">25%</button>
                <button type="button" class="editor-btn" style="padding: 0.2rem 0.45rem; font-size: 0.75rem;" onclick="resizeSelectedImg('50%')">50%</button>
                <button type="button" class="editor-btn" style="padding: 0.2rem 0.45rem; font-size: 0.75rem;" onclick="resizeSelectedImg('75%')">75%</button>
                <button type="button" class="editor-btn" style="padding: 0.2rem 0.45rem; font-size: 0.75rem;" onclick="resizeSelectedImg('100%')">100%</button>
                <span style="border-left: 1px solid var(--border-color); height: 16px; margin: 0 0.25rem;"></span>
                <button type="button" class="editor-btn" style="padding: 0.2rem 0.45rem; font-size: 0.75rem; color: var(--danger);" onclick="deleteSelectedImg()"><i class="fa-solid fa-trash"></i> Hapus</button>
            </div>

            <!-- Dynamic Rich WYSIWYG Editors for Options -->
            <div id="optionsContainer">
                <div id="dynamicOptionsList" style="display: flex; flex-direction: column; gap: 1rem;">
                    <!-- Option items will be rendered dynamically by JS -->
                </div>
                <button type="button" class="btn btn-secondary" onclick="addOptionField()" style="margin-top: 1rem; width: 100%; border-style: dashed;">
                    <i class="fa-solid fa-plus"></i> Tambah Opsi Jawaban Baru
                </button>
            </div>

            <input type="file" id="mediaFileInput" style="display: none;" onchange="handleMediaUpload(this.files[0])">

            <div class="form-group" style="margin-top: 1.25rem;">
                <label for="correct_answer" id="correctAnswerLabel">Kunci Jawaban</label>
                <input type="text" name="correct_answer" id="correct_answer" class="form-control" placeholder="Contoh: B (atau fact / opinion / A,C atau json pairs / ordered items)">
                <small style="color: var(--text-muted); display: block; margin-top: 0.25rem;" id="correctAnswerHint">
                    Pilihan Ganda: A/B/C/D/E. Pilihan Banyak: A,B,C. Fakta/Opini: fact atau opinion. Mengurutkan: Item1,Item2,Item3.
                </small>
            </div>

            <div class="form-group">
                <label for="explanation">Pembahasan / Penjelasan Soal (Optional)</label>
                <textarea name="explanation" class="form-control" rows="2" placeholder="Masukkan pembahasan soal jika ada..."></textarea>
            </div>

            <div class="form-group">
                <label for="weight">Bobot Nilai Soal (Weight)</label>
                <input type="number" name="weight" class="form-control" value="10" min="1" required>
            </div>

            <button type="submit" class="btn btn-primary" id="formSubmitBtn" style="width: 100%;">Simpan Soal ke Bank Soal</button>
        </form>
    </div>

    <div class="card">
        <div class="card-header">Daftar Soal dalam Kelompok Ini ({{ $group->questions->count() }})</div>
        <div style="display: flex; flex-direction: column; gap: 1.25rem;">
            @forelse($group->questions as $index => $q)
                <div style="background: var(--bg-body); padding: 1.25rem; border-radius: 0.5rem; border: 1px solid var(--border-color);">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem;">
                        <span style="font-weight: 700; color: var(--primary);">#{{ $index + 1 }} ({{ strtoupper(str_replace('_', ' ', $q->question_type)) }})</span>
                        <div style="display: flex; align-items: center; gap: 0.6rem;">
                            <span style="color: var(--accent); font-weight: 600; font-size: 0.9rem; margin-right: 0.5rem;">Bobot: {{ $q->weight }}</span>
                            <button type="button" class="btn btn-secondary" onclick="editQuestionById({{ $q->id }})" style="padding: 0.25rem 0.6rem; font-size: 0.8rem; color: var(--primary); border-color: var(--primary);" title="Edit Soal">
                                <i class="fa-solid fa-pen-to-square"></i> Edit
                            </button>
                            <form action="{{ route('teacher.questions.destroy', $q->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus soal ini beserta seluruh gambar/file S3 di dalamnya?');" style="display: inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-secondary" style="padding: 0.25rem 0.6rem; font-size: 0.8rem; color: var(--danger); border-color: var(--danger);" title="Hapus Soal & Berkas S3">
                                    <i class="fa-solid fa-trash-can"></i> Hapus
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- Rich HTML Rendered Question Content -->
                    <div style="margin-bottom: 1rem; line-height: 1.7; font-size: 1rem; color: var(--text-main);" dir="auto">
                        {!! \App\Services\HtmlSanitizerService::sanitize($q->content) !!}
                    </div>

                    @if($q->options_json)
                        @if($q->question_type === 'matching')
                            <div style="font-size: 0.85rem; color: var(--text-muted); margin-bottom: 0.5rem;">
                                Pasangan: {{ json_encode($q->options_json) }}
                            </div>
                        @elseif(is_array($q->options_json))
                            <ul style="list-style: none; padding-left: 1rem; color: var(--text-muted); font-size: 0.9rem;">
                                @foreach($q->options_json as $opt)
                                    @if(is_array($opt))
                                        <li style="margin-bottom: 0.4rem;"><strong>{{ $opt['id'] ?? '' }}.</strong> {!! \App\Services\HtmlSanitizerService::sanitize($opt['text'] ?? '') !!}</li>
                                    @else
                                        <li style="margin-bottom: 0.4rem;">• {{ $opt }}</li>
                                    @endif
                                @endforeach
                            </ul>
                        @endif
                    @endif

                    @if($q->correct_answers_json)
                        <div style="margin-top: 0.5rem; font-size: 0.85rem; color: var(--status-answered); font-weight: 600;">
                            Kunci Jawaban: {{ is_array($q->correct_answers_json) ? implode(', ', $q->correct_answers_json) : (is_array($q->correct_answers_json) ? json_encode($q->correct_answers_json) : $q->correct_answers_json) }}
                        </div>
                    @else
                        <div style="margin-top: 0.5rem; font-size: 0.85rem; color: var(--warning); font-weight: 600;">
                            Kunci Jawaban: (Penilaian Manual Guru / Esai)
                        </div>
                    @endif

                    @if($q->explanation)
                        <div style="margin-top: 0.4rem; font-size: 0.85rem; color: var(--text-muted); font-style: italic;">
                            Pembahasan: {{ $q->explanation }}
                        </div>
                    @endif
                </div>
            @empty
                <p style="text-align: center; color: var(--text-muted);">Belum ada soal dalam kelompok ini.</p>
            @endforelse
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
let currentUploadType = 'image';
let currentUploadTargetId = 'editorContent';
let selectedImg = null;
let optionCount = 0;
const optionLabels = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P'];
const QUESTIONS_BY_ID = {{ Illuminate\Support\Js::from($group->questions->keyBy('id')) }};

function execCmd(command, value = null) {
    document.execCommand(command, false, value);
    document.getElementById('editorContent').focus();
}

function execOptionCmd(editorId, command, value = null) {
    const el = document.getElementById(editorId);
    if (el) {
        el.focus();
        document.execCommand(command, false, value);
    }
}

function showUploadLoading(fileName) {
    const overlay = document.getElementById('uploadLoadingOverlay');
    const textEl = document.getElementById('uploadLoadingText');
    if (textEl && fileName) {
        textEl.textContent = `Mengunggah & mengompresi "${fileName}" ke S3 Storage. Mohon tunggu...`;
    }
    if (overlay) overlay.style.display = 'flex';
}

function hideUploadLoading() {
    const overlay = document.getElementById('uploadLoadingOverlay');
    if (overlay) overlay.style.display = 'none';
}

function createOptionHTML(index) {
    const label = optionLabels[index] || ('P' + (index + 1));
    const editorId = `editorOption_${index}`;
    return `
        <div class="form-group option-item-row" id="optionRow_${index}" style="background: var(--bg-body); padding: 0.85rem; border-radius: 0.5rem; border: 1px solid var(--border-color);">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.4rem;">
                <label style="margin: 0; font-weight: 600;">Pilihan ${label} (WYSIWYG Editor)</label>
                <button type="button" class="btn btn-secondary btn-remove-option" onclick="removeOptionField(${index})" style="padding: 0.15rem 0.5rem; font-size: 0.75rem; color: var(--danger); border-color: var(--danger);" title="Hapus Opsi">
                    <i class="fa-solid fa-trash-can"></i> Hapus Opsi
                </button>
            </div>
            <div class="editor-toolbar" style="padding: 0.25rem 0.5rem;">
                <button type="button" class="editor-btn" style="padding: 0.2rem 0.45rem; font-size: 0.75rem;" onclick="execOptionCmd('${editorId}', 'bold')"><i class="fa-solid fa-bold"></i></button>
                <button type="button" class="editor-btn" style="padding: 0.2rem 0.45rem; font-size: 0.75rem;" onclick="execOptionCmd('${editorId}', 'italic')"><i class="fa-solid fa-italic"></i></button>
                <button type="button" class="editor-btn" style="padding: 0.2rem 0.45rem; font-size: 0.75rem;" onclick="execOptionCmd('${editorId}', 'underline')"><i class="fa-solid fa-underline"></i></button>
                <button type="button" class="editor-btn" style="padding: 0.2rem 0.45rem; font-size: 0.75rem;" onclick="triggerMediaUpload('image', '${editorId}')" title="Sisipkan Gambar">
                    <i class="fa-solid fa-image" style="color: var(--primary);"></i> Gambar
                </button>
                <button type="button" class="editor-btn" style="padding: 0.2rem 0.45rem; font-size: 0.75rem;" onclick="insertYoutubeVideo('${editorId}')" title="Embed YouTube">
                    <i class="fa-brands fa-youtube" style="color: #ef4444;"></i> YouTube
                </button>
                <button type="button" class="editor-btn" style="padding: 0.2rem 0.45rem; font-size: 0.75rem;" onclick="triggerMediaUpload('pdf', '${editorId}')" title="Dokumen PDF">
                    <i class="fa-solid fa-file-pdf" style="color: #dc2626;"></i> PDF
                </button>
                <button type="button" class="editor-btn" style="padding: 0.2rem 0.45rem; font-size: 0.75rem;" onclick="execOptionCmd('${editorId}', 'removeFormat')"><i class="fa-solid fa-eraser"></i></button>
            </div>
            <div id="${editorId}" class="option-editor-content" contenteditable="true"></div>
            <input type="hidden" name="options[]" id="hiddenOption_${index}">
        </div>
    `;
}

function renderInitialOptions() {
    const list = document.getElementById('dynamicOptionsList');
    list.innerHTML = '';
    optionCount = 0;
    // Initial 4 options (A, B, C, D)
    for (let i = 0; i < 4; i++) {
        addOptionField();
    }
}

function addOptionField() {
    const list = document.getElementById('dynamicOptionsList');
    const index = optionCount;
    const div = document.createElement('div');
    div.innerHTML = createOptionHTML(index);
    list.appendChild(div.firstElementChild);
    optionCount++;
    updateOptionLabelsAndButtons();
}

function removeOptionField(index) {
    const rows = document.querySelectorAll('.option-item-row');
    if (rows.length <= 2) {
        alert('Minimal harus ada 2 pilihan jawaban!');
        return;
    }
    const targetRow = document.getElementById(`optionRow_${index}`);
    if (targetRow) {
        targetRow.remove();
        updateOptionLabelsAndButtons();
    }
}

function updateOptionLabelsAndButtons() {
    const rows = document.querySelectorAll('.option-item-row');
    rows.forEach((row, i) => {
        const label = optionLabels[i] || ('P' + (i + 1));
        const labelEl = row.querySelector('label');
        if (labelEl) labelEl.textContent = `Pilihan ${label} (WYSIWYG Editor)`;

        const btnRemove = row.querySelector('.btn-remove-option');
        if (btnRemove) {
            btnRemove.style.display = rows.length <= 2 ? 'none' : 'inline-flex';
        }
    });
}

function editQuestionById(questionId) {
    const q = QUESTIONS_BY_ID[questionId];
    if (!q) return;

    const cardHeader = document.getElementById('formCardHeader');
    const form = document.getElementById('createQuestionForm');
    const submitBtn = document.getElementById('formSubmitBtn');
    const cancelBtn = document.getElementById('cancelEditBtn');
    const methodContainer = document.getElementById('formMethodContainer');

    if (cardHeader) cardHeader.textContent = `Edit Soal #${q.id}`;
    if (submitBtn) submitBtn.textContent = 'Perbarui Soal';
    if (cancelBtn) cancelBtn.style.display = 'inline-flex';
    if (form) form.action = `/teacher/questions/${q.id}`;
    if (methodContainer) methodContainer.innerHTML = '<input type="hidden" name="_method" value="PUT">';

    // Set question type
    const typeSelect = document.getElementById('question_type');
    if (typeSelect) {
        typeSelect.value = q.question_type;
        toggleOptionFields(q.question_type);
    }

    // Set content
    const editor = document.getElementById('editorContent');
    if (editor) editor.innerHTML = q.content || '';

    // Set options
    const list = document.getElementById('dynamicOptionsList');
    list.innerHTML = '';
    optionCount = 0;

    if (q.options_json && Array.isArray(q.options_json) && q.options_json.length > 0) {
        q.options_json.forEach(opt => {
            const index = optionCount;
            const div = document.createElement('div');
            div.innerHTML = createOptionHTML(index);
            list.appendChild(div.firstElementChild);

            const optionEditor = document.getElementById(`editorOption_${index}`);
            if (optionEditor) {
                optionEditor.innerHTML = (typeof opt === 'object' && opt !== null) ? (opt.text || '') : opt;
            }
            optionCount++;
        });
        updateOptionLabelsAndButtons();
    } else {
        renderInitialOptions();
    }

    // Set correct answer & explanation & weight
    const correctAnswerInput = document.getElementById('correct_answer');
    if (correctAnswerInput) {
        if (q.correct_answers_json) {
            correctAnswerInput.value = Array.isArray(q.correct_answers_json) ? q.correct_answers_json.join(', ') : (typeof q.correct_answers_json === 'object' ? JSON.stringify(q.correct_answers_json) : q.correct_answers_json);
        } else {
            correctAnswerInput.value = '';
        }
    }

    const explanationInput = document.querySelector('textarea[name="explanation"]');
    if (explanationInput) explanationInput.value = q.explanation || '';

    const weightInput = document.querySelector('input[name="weight"]');
    if (weightInput) weightInput.value = q.weight || 10;

    // Scroll smoothly to form
    document.getElementById('formCard').scrollIntoView({ behavior: 'smooth' });
}

function cancelEdit() {
    const cardHeader = document.getElementById('formCardHeader');
    const form = document.getElementById('createQuestionForm');
    const submitBtn = document.getElementById('formSubmitBtn');
    const cancelBtn = document.getElementById('cancelEditBtn');
    const methodContainer = document.getElementById('formMethodContainer');

    if (cardHeader) cardHeader.textContent = 'Tambah Soal Baru';
    if (submitBtn) submitBtn.textContent = 'Simpan Soal ke Bank Soal';
    if (cancelBtn) cancelBtn.style.display = 'none';
    if (form) form.action = "{{ route('teacher.questions.store', $group->id) }}";
    if (methodContainer) methodContainer.innerHTML = '';

    form.reset();
    document.getElementById('editorContent').innerHTML = '';
    renderInitialOptions();
}

document.addEventListener("DOMContentLoaded", function () {
    renderInitialOptions();

    const editor = document.getElementById('editorContent');
    const form = document.getElementById('createQuestionForm');
    const popover = document.getElementById('imageResizerPopover');

    // Interactive Image Selection & Floating Toolbar Positioning
    document.addEventListener('click', function (e) {
        if (e.target && e.target.tagName === 'IMG' && (e.target.closest('#editorContent') || e.target.closest('.option-editor-content'))) {
            if (selectedImg) {
                selectedImg.classList.remove('selected-editor-img');
            }
            selectedImg = e.target;
            selectedImg.classList.add('selected-editor-img');

            const rect = selectedImg.getBoundingClientRect();
            popover.style.display = 'flex';
            popover.style.top = (window.scrollY + rect.top - 42) + 'px';
            popover.style.left = (window.scrollX + rect.left) + 'px';
        } else if (!e.target.closest('#imageResizerPopover')) {
            hideImageResizer();
        }
    });

    document.addEventListener('scroll', hideImageResizer);

    form.addEventListener('submit', function (e) {
        // Remove selection highlight before saving HTML
        if (selectedImg) {
            selectedImg.classList.remove('selected-editor-img');
        }

        const html = editor.innerHTML.trim();
        const textContent = editor.innerText.trim();
        
        // Prevent empty form submission
        if (textContent.length === 0 && !html.includes('<img') && !html.includes('<iframe') && !html.includes('pdf-attachment-badge')) {
            e.preventDefault();
            alert('Isi pertanyaan tidak boleh kosong!');
            return false;
        }

        document.getElementById('hiddenQuestionContent').value = html;

        // Sync dynamic option editors to hidden inputs
        const rows = document.querySelectorAll('.option-item-row');
        rows.forEach(row => {
            const editorDiv = row.querySelector('.option-editor-content');
            const hiddenInput = row.querySelector('input[name="options[]"]');
            if (editorDiv && hiddenInput) {
                hiddenInput.value = editorDiv.innerHTML.trim();
            }
        });
    });
});

function hideImageResizer() {
    const popover = document.getElementById('imageResizerPopover');
    if (popover) popover.style.display = 'none';
    if (selectedImg) {
        selectedImg.classList.remove('selected-editor-img');
        selectedImg = null;
    }
}

function resizeSelectedImg(widthStr) {
    if (selectedImg) {
        selectedImg.style.width = widthStr;
        selectedImg.style.height = 'auto';
        hideImageResizer();
    }
}

function deleteSelectedImg() {
    if (selectedImg) {
        const imgUrl = selectedImg.getAttribute('src');
        selectedImg.remove();
        hideImageResizer();

        if (imgUrl) {
            fetch('{{ route("teacher.media.delete") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ url: imgUrl })
            })
            .then(res => res.json())
            .then(data => {
                console.log('Storage cleanup:', data.message);
            })
            .catch(err => {
                console.warn('Failed to delete media from storage:', err);
            });
        }
    }
}

function triggerMediaUpload(type, targetId = 'editorContent') {
    currentUploadType = type;
    currentUploadTargetId = targetId;
    const fileInput = document.getElementById('mediaFileInput');
    if (type === 'image') {
        fileInput.accept = 'image/*';
    } else if (type === 'pdf') {
        fileInput.accept = 'application/pdf';
    }
    fileInput.click();
}

function handleMediaUpload(file) {
    if (!file) return;

    showUploadLoading(file.name);

    const formData = new FormData();
    formData.append('file', file);
    formData.append('_token', '{{ csrf_token() }}');

    const targetEl = document.getElementById(currentUploadTargetId);
    if (targetEl) targetEl.focus();

    fetch('{{ route("teacher.media.upload") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        hideUploadLoading();
        if (data.status === 'success') {
            if (data.is_pdf) {
                const safeName = escapeHtml(data.original_name);
                const pdfHtml = `<p><a href="${data.url}" target="_blank" class="pdf-attachment-badge"><i class="fa-solid fa-file-pdf"></i> Unduh Lampiran PDF (${safeName})</a></p><p><br></p>`;
                document.execCommand('insertHTML', false, pdfHtml);
            } else {
                const imgHtml = `<p><img src="${data.url}" alt="Gambar Pilihan" style="max-width: 100%; height: auto; border-radius: 0.5rem; margin: 0.5rem 0;"></p><p><br></p>`;
                document.execCommand('insertHTML', false, imgHtml);
            }
        } else {
            alert('Upload gagal: ' + (data.message || 'Error server'));
        }
    })
    .catch(err => {
        hideUploadLoading();
        alert('Upload gagal: ' + err.message);
    })
    .finally(() => {
        document.getElementById('mediaFileInput').value = '';
    });
}

function escapeHtml(value) {
    const element = document.createElement('span');
    element.textContent = String(value ?? '');
    return element.innerHTML;
}

function insertYoutubeVideo(targetId = 'editorContent') {
    const url = prompt('Masukkan URL Video YouTube (Contoh: https://www.youtube.com/watch?v=VIDEO_ID atau https://youtu.be/VIDEO_ID):');
    if (!url) return;

    let videoId = '';
    const regExp = /^.*(youtu.be\/|v\/|u\/\w\/|embed\/|watch\?v=|\&v=)([^#\&\?]*).*/;
    const match = url.match(regExp);

    if (match && match[2].length === 11) {
        videoId = match[2];
        const embedHtml = `<div class="video-responsive-container"><iframe src="https://www.youtube.com/embed/${videoId}" allowfullscreen></iframe></div><p><br></p>`;
        const targetEl = document.getElementById(targetId);
        if (targetEl) {
            targetEl.focus();
            document.execCommand('insertHTML', false, embedHtml);
        }
    } else {
        alert('URL Video YouTube tidak valid!');
    }
}

function toggleOptionFields(type) {
    const container = document.getElementById('optionsContainer');
    const label = document.getElementById('correctAnswerLabel');
    const hint = document.getElementById('correctAnswerHint');
    const input = document.getElementById('correct_answer');
    
    if (type === 'single_choice' || type === 'multiple_choice') {
        container.style.display = 'block';
        label.textContent = 'Kunci Jawaban';
        hint.textContent = 'Pilihan Ganda: A/B/C/D/E. Pilihan Banyak: A,B,C';
        input.required = true;
    } else {
        container.style.display = 'none';
        if (type === 'essay') {
            label.textContent = 'Kunci Jawaban / Rubrik Penilaian (Opsional)';
            hint.textContent = 'Kunci/Rubrik bersifat OPSIONAL untuk soal esai. Jawaban penalaran siswa dapat dinilai secara manual oleh guru.';
            input.required = false;
        } else {
            label.textContent = 'Kunci Jawaban';
            input.required = true;
            if (type === 'fact_opinion') {
                hint.textContent = 'Masukkan: "fact" atau "opinion"';
            } else if (type === 'true_false') {
                hint.textContent = 'Masukkan: "true" atau "false"';
            } else if (type === 'sorting') {
                hint.textContent = 'Masukkan urutan yang benar dipisah koma, contoh: Langkah 1, Langkah 2, Langkah 3';
            } else if (type === 'matching') {
                hint.textContent = 'Masukkan pasangan JSON contoh: {"Indonesia":"Jakarta","Jepang":"Tokyo"}';
            } else {
                hint.textContent = 'Masukkan teks kunci jawaban yang benar';
            }
        }
    }
}
</script>
@endsection
