@extends('layouts.app')

@section('title', 'Manage Questions - ' . $group->name)

@section('styles')
<link rel="stylesheet" href="{{ asset('vendor/quill/quill.snow.css') }}">
<style>
    .ql-toolbar {
        border-radius: 0.5rem 0.5rem 0 0 !important;
        border-color: var(--border-color) !important;
        background: var(--bg-card-hover) !important;
    }
    .ql-container {
        border-radius: 0 0 0.5rem 0.5rem !important;
        border-color: var(--border-color) !important;
        background: var(--bg-card) !important;
        color: var(--text-main) !important;
        font-family: inherit !important;
    }
    .ql-editor {
        min-height: 180px !important;
        font-size: 1rem !important;
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

    <div class="card">
        <div class="card-header">Tambah Soal Baru</div>
        <form action="{{ route('teacher.questions.store', $group->id) }}" method="POST" id="createQuestionForm">
            @csrf
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

            <!-- WYSIWYG Editor Container -->
            <div class="form-group">
                <label for="content">Isi Pertanyaan / Instruksi Soal (WYSIWYG Rich Editor)</label>
                <div id="quillToolbar">
                    <span class="ql-formats">
                        <select class="ql-header">
                            <option selected></option>
                            <option value="1">Heading 1</option>
                            <option value="2">Heading 2</option>
                        </select>
                    </span>
                    <span class="ql-formats">
                        <button class="ql-bold" title="Bold"></button>
                        <button class="ql-italic" title="Italic"></button>
                        <button class="ql-underline" title="Underline"></button>
                        <button class="ql-strike" title="Strike"></button>
                    </span>
                    <span class="ql-formats">
                        <select class="ql-color" title="Text Color"></select>
                        <select class="ql-background" title="Background Color"></select>
                    </span>
                    <span class="ql-formats">
                        <button class="ql-list" value="ordered" title="Numbered List"></button>
                        <button class="ql-list" value="bullet" title="Bulleted List"></button>
                    </span>
                    <span class="ql-formats">
                        <button type="button" onclick="triggerMediaUpload('image')" title="Upload & Resizing Gambar (Max 1024x1024)"><i class="fa-solid fa-image"></i></button>
                        <button type="button" onclick="insertYoutubeVideo()" title="Embed Video YouTube"><i class="fa-brands fa-youtube" style="color: #ef4444;"></i></button>
                        <button type="button" onclick="triggerMediaUpload('pdf')" title="Lampirkan Dokumen PDF (Max 5MB)"><i class="fa-solid fa-file-pdf" style="color: #dc2626;"></i></button>
                        <button class="ql-link" title="Insert Link"></button>
                        <button class="ql-clean" title="Remove Formatting"></button>
                    </span>
                </div>
                <div id="quillEditor"></div>
                <input type="hidden" name="content" id="hiddenQuestionContent" required>
                <input type="file" id="mediaFileInput" style="display: none;" onchange="handleMediaUpload(this.files[0])">
            </div>

            <div id="optionsContainer">
                <div class="form-group">
                    <label>Pilihan A</label>
                    <input type="text" name="option_a" class="form-control">
                </div>
                <div class="form-group">
                    <label>Pilihan B</label>
                    <input type="text" name="option_b" class="form-control">
                </div>
                <div class="form-group">
                    <label>Pilihan C</label>
                    <input type="text" name="option_c" class="form-control">
                </div>
                <div class="form-group">
                    <label>Pilihan D</label>
                    <input type="text" name="option_d" class="form-control">
                </div>
            </div>

            <div class="form-group">
                <label for="correct_answer" id="correctAnswerLabel">Kunci Jawaban</label>
                <input type="text" name="correct_answer" id="correct_answer" class="form-control" placeholder="Contoh: B (atau fact / opinion / A,C atau json pairs / ordered items)">
                <small style="color: var(--text-muted); display: block; margin-top: 0.25rem;" id="correctAnswerHint">
                    Pilihan Ganda: A/B/C/D. Pilihan Banyak: A,B,C. Fakta/Opini: fact atau opinion. Mengurutkan: Item1,Item2,Item3.
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

            <button type="submit" class="btn btn-primary" style="width: 100%;">Simpan Soal ke Bank Soal</button>
        </form>
    </div>

    <div class="card">
        <div class="card-header">Daftar Soal dalam Kelompok Ini ({{ $group->questions->count() }})</div>
        <div style="display: flex; flex-direction: column; gap: 1.25rem;">
            @forelse($group->questions as $index => $q)
                <div style="background: var(--bg-body); padding: 1.25rem; border-radius: 0.5rem; border: 1px solid var(--border-color);">
                    <div style="display: flex; justify-content: space-between; margin-bottom: 0.75rem;">
                        <span style="font-weight: 700; color: var(--primary);">#{{ $index + 1 }} ({{ strtoupper(str_replace('_', ' ', $q->question_type)) }})</span>
                        <span style="color: var(--accent); font-weight: 600; font-size: 0.9rem;">Bobot: {{ $q->weight }}</span>
                    </div>

                    <!-- Rich HTML Rendered Question Content -->
                    <div style="margin-bottom: 1rem; line-height: 1.7; font-size: 1rem; color: var(--text-main);" dir="auto">
                        {!! $q->content !!}
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
                                        <li>{{ $opt['id'] ?? '' }}. {{ $opt['text'] ?? '' }}</li>
                                    @else
                                        <li>• {{ $opt }}</li>
                                    @endif
                                @endforeach
                            </ul>
                        @endif
                    @endif

                    @if($q->correct_answers_json)
                        <div style="margin-top: 0.5rem; font-size: 0.85rem; color: var(--status-answered); font-weight: 600;">
                            Kunci Jawaban: {{ is_array($q->correct_answers_json) ? implode(', ', $q->correct_answers_json) : $q->correct_answers_json }}
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
<script src="{{ asset('vendor/quill/quill.min.js') }}"></script>
<script>
let quill;
let currentUploadType = 'image';

document.addEventListener("DOMContentLoaded", function () {
    quill = new Quill('#quillEditor', {
        modules: {
            toolbar: '#quillToolbar'
        },
        placeholder: 'Ketik isi soal atau instruksi di sini... Gunakan toolbar untuk menambahkan Gambar, Video YouTube, atau Dokumen PDF.',
        theme: 'snow'
    });

    document.getElementById('createQuestionForm').addEventListener('submit', function (e) {
        const html = quill.root.innerHTML;
        const textContent = quill.getText().trim();
        
        // Prevent empty form submission
        if (textContent.length === 0 && !html.includes('<img') && !html.includes('<iframe') && !html.includes('pdf-attachment-badge')) {
            e.preventDefault();
            alert('Isi pertanyaan tidak boleh kosong!');
            return false;
        }

        document.getElementById('hiddenQuestionContent').value = html;
    });
});

function triggerMediaUpload(type) {
    currentUploadType = type;
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

    const formData = new FormData();
    formData.append('file', file);
    formData.append('_token', '{{ csrf_token() }}');

    // Show loading state
    const range = quill.getSelection(true);
    quill.insertText(range.index, '[Uploading media...]', 'italic', true);

    fetch('{{ route("teacher.media.upload") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        // Remove placeholder text
        quill.deleteText(range.index, 20);

        if (data.status === 'success') {
            if (data.is_pdf) {
                // Insert PDF badge
                const pdfHtml = `<p><a href="${data.url}" target="_blank" class="pdf-attachment-badge"><i class="fa-solid fa-file-pdf"></i> Unduh Lampiran PDF (${data.original_name})</a></p>`;
                quill.clipboard.dangerouslyPasteHTML(range.index, pdfHtml);
            } else {
                // Insert Resized Image
                quill.insertEmbed(range.index, 'image', data.url);
            }
        } else {
            alert('Upload gagal: ' + (data.message || 'Error server'));
        }
    })
    .catch(err => {
        quill.deleteText(range.index, 20);
        alert('Upload gagal: ' + err.message);
    });

    // Reset input
    document.getElementById('mediaFileInput').value = '';
}

function insertYoutubeVideo() {
    const url = prompt('Masukkan URL Video YouTube (Contoh: https://www.youtube.com/watch?v=VIDEO_ID atau https://youtu.be/VIDEO_ID):');
    if (!url) return;

    let videoId = '';
    const regExp = /^.*(youtu.be\/|v\/|u\/\w\/|embed\/|watch\?v=|\&v=)([^#\&\?]*).*/;
    const match = url.match(regExp);

    if (match && match[2].length === 11) {
        videoId = match[2];
        const embedHtml = `<div class="video-responsive-container"><iframe src="https://www.youtube.com/embed/${videoId}" allowfullscreen></iframe></div>`;
        const range = quill.getSelection(true);
        quill.clipboard.dangerouslyPasteHTML(range.index, embedHtml);
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
        hint.textContent = 'Pilihan Ganda: A/B/C/D. Pilihan Banyak: A,B,C';
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
