@extends('layouts.app')

@section('title', 'Ajenono - Aplikasi Ujian Elektronik Online V2')

@section('styles')
<style>
    .hero-section {
        text-align: center;
        padding: 4rem 1.5rem;
        background: linear-gradient(135deg, var(--bg-card) 0%, var(--bg-card-hover) 100%);
        border-radius: 1rem;
        border: 1px solid var(--border-color);
        margin-bottom: 3rem;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
    }
    .hero-title {
        font-size: 2.5rem;
        font-weight: 800;
        color: var(--primary);
        margin-bottom: 1rem;
        line-height: 1.2;
    }
    .hero-subtitle {
        font-size: 1.15rem;
        color: var(--text-muted);
        max-width: 700px;
        margin: 0 auto 2rem auto;
    }
    .arabic-text {
        font-family: 'KFGQPC Uthman Taha Naskh', 'Amiri', serif;
        font-size: 1.8rem;
        color: var(--primary);
        margin-bottom: 0.5rem;
        direction: rtl;
    }
    .hero-actions {
        display: flex;
        gap: 1rem;
        justify-content: center;
        flex-wrap: wrap;
    }
    .hero-actions .btn {
        padding: 0.75rem 1.5rem;
        font-size: 1rem;
    }
    .features-grid {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        gap: 1.5rem;
        margin-bottom: 3rem;
    }
    .feature-card {
        flex: 1 1 250px;
        max-width: 400px;
        background-color: var(--bg-card);
        border: 1px solid var(--border-color);
        border-radius: 0.75rem;
        padding: 1.5rem;
        text-align: center;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .feature-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        border-color: var(--primary);
    }
    .feature-icon {
        font-size: 2.5rem;
        color: var(--accent);
        margin-bottom: 1rem;
    }
    .feature-title {
        font-size: 1.2rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
        color: var(--text-main);
    }
    .feature-desc {
        font-size: 0.95rem;
        color: var(--text-muted);
        line-height: 1.5;
    }
    .jargon-badge {
        display: inline-block;
        background-color: rgba(99, 102, 241, 0.1);
        color: var(--primary);
        padding: 0.4rem 1rem;
        border-radius: 2rem;
        font-size: 0.85rem;
        font-weight: 600;
        margin-bottom: 1.5rem;
        border: 1px solid rgba(99, 102, 241, 0.2);
    }
    
    [data-theme="dark"] .jargon-badge {
        background-color: rgba(99, 102, 241, 0.15);
        color: var(--accent);
        border-color: rgba(99, 102, 241, 0.3);
    }
    
    @media (max-width: 768px) {
        .hero-title {
            font-size: 2rem;
        }
        .hero-subtitle {
            font-size: 1rem;
        }
        .hero-section {
            padding: 3rem 1rem;
        }
    }
</style>
@endsection

@section('content')
<div class="hero-section">
    <div class="arabic-text">بِسْمِ اللَّهِ الرَّحْمَنِ الرَّحِيم</div>
    <div class="arabic-text" style="font-size: 1.4rem; margin-bottom: 1.5rem;">اللَّهُمَّ صَلِّ وَسَلِّمْ عَلَى سَيِّدِنَا مُحَمَّدٍ وَعَلَى آلِ سَيِّدِنَا مُحَمَّدٍ</div>
    
    <div class="jargon-badge"><i class="fa-solid fa-hand-holding-heart"></i> Berkhidmat untuk Umat</div>
    
    <h1 class="hero-title">Platform Ujian Online Terbuka</h1>
    <p class="hero-subtitle">
        Sistem ujian digital berkecepatan tinggi, aman, dan mudah diakses. Dibangun demi kemaslahatan umat terkhusus pendidikan.
    </p>
    
    <div class="hero-actions">
        <a href="{{ route('login') }}" class="btn btn-primary"><i class="fa-solid fa-right-to-bracket" style="margin-right: 0.5rem;"></i> Masuk ke Portal</a>
        <a href="{{ route('register.school') }}" class="btn btn-secondary"><i class="fa-solid fa-school" style="margin-right: 0.5rem;"></i> Daftarkan Sekolah</a>
    </div>
</div>

<div class="features-grid">
    <div class="feature-card">
        <div class="feature-icon"><i class="fa-solid fa-bolt"></i></div>
        <h3 class="feature-title">Performa Tinggi</h3>
        <p class="feature-desc">Dioptimalkan untuk ratusan peserta ujian secara bersamaan tanpa kendala, bersahabat dengan <i>shared hosting</i>.</p>
    </div>
    <div class="feature-card">
        <div class="feature-icon"><i class="fa-solid fa-list-check"></i></div>
        <h3 class="feature-title">7 Tipe Soal</h3>
        <p class="feature-desc">Mendukung pilihan ganda, ganda kompleks, esai, benar-salah, fakta-opini, menjodohkan, hingga mengurutkan.</p>
    </div>
    <div class="feature-card">
        <div class="feature-icon"><i class="fa-solid fa-shield-halved"></i></div>
        <h3 class="feature-title">Aman & Mandiri</h3>
        <p class="feature-desc">Tanpa dependensi CDN eksternal. Sepenuhnya berjalan lokal untuk meminimalisir gangguan suplai pihak ketiga.</p>
    </div>
    <div class="feature-card">
        <div class="feature-icon"><i class="fa-solid fa-users-gear"></i></div>
        <h3 class="feature-title">Multi-Peran</h3>
        <p class="feature-desc">Sistem terintegrasi untuk Administrator Sekolah, Guru pengampu, hingga Siswa peserta ujian dalam satu atap.</p>
    </div>
</div>
@endsection
