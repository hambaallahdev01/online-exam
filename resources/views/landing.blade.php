@extends('layouts.app')

@section('title', __('messages.hero_title') . ' - ' . __('messages.brand_name'))

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
    
    .badge-lang {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        padding: 0.35rem 0.75rem;
        border-radius: 2rem;
        font-size: 0.85rem;
        font-weight: 600;
        text-decoration: none;
        color: var(--text-muted);
        background: var(--bg-card);
        border: 1px solid var(--border-color);
        transition: all 0.2s ease;
    }
    .badge-lang:hover {
        border-color: var(--primary);
        color: var(--primary);
        transform: translateY(-2px);
    }
    .badge-lang.active {
        background: rgba(99, 102, 241, 0.15);
        color: var(--primary);
        border-color: var(--accent);
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
    <div class="arabic-text">{{ __('messages.bismillah') }}</div>
    <div class="arabic-text" style="font-size: 1.4rem; margin-bottom: 1.5rem;">{{ __('messages.shalawat') }}</div>
    
    <div class="jargon-badge"><i class="fa-solid fa-hand-holding-heart"></i> {{ __('messages.jargon') }}</div>
    
    <h1 class="hero-title">{{ __('messages.hero_title') }}</h1>
    <p class="hero-subtitle">
        {{ __('messages.hero_subtitle') }}
    </p>
    
    <div class="hero-actions">
        <a href="{{ route('login') }}" class="btn btn-primary"><i class="fa-solid fa-right-to-bracket" style="margin-right: 0.5rem;"></i> {{ __('messages.enter_portal') }}</a>
        <a href="{{ route('register.school') }}" class="btn btn-secondary"><i class="fa-solid fa-school" style="margin-right: 0.5rem;"></i> {{ __('messages.register_school_btn') }}</a>
    </div>

    <!-- Landing Quick Language Switcher with Font Awesome Flags -->
    <div style="margin-top: 2rem; display: flex; align-items: center; justify-content: center; gap: 0.5rem; flex-wrap: wrap;">
        <span style="font-size: 0.85rem; color: var(--text-muted); font-weight: 500;">
            <i class="fa-solid fa-flag" style="color: var(--accent);"></i> {{ __('messages.select_language') }}:
        </span>
        <a href="{{ route('set.locale', 'id') }}" class="badge-lang {{ app()->getLocale() === 'id' ? 'active' : '' }}" title="Bahasa Indonesia">
            <i class="fa-solid fa-flag" style="color: #ef4444;"></i> 🇮🇩 ID
        </a>
        <a href="{{ route('set.locale', 'en') }}" class="badge-lang {{ app()->getLocale() === 'en' ? 'active' : '' }}" title="English (GB)">
            <i class="fa-solid fa-flag" style="color: #3b82f6;"></i> 🇬🇧 EN
        </a>
        <a href="{{ route('set.locale', 'ar') }}" class="badge-lang {{ app()->getLocale() === 'ar' ? 'active' : '' }}" title="العربية">
            <i class="fa-solid fa-flag" style="color: #10b981;"></i> 🇸🇦 AR
        </a>
        <a href="{{ route('set.locale', 'zh') }}" class="badge-lang {{ app()->getLocale() === 'zh' ? 'active' : '' }}" title="中文">
            <i class="fa-solid fa-flag" style="color: #f59e0b;"></i> 🇨🇳 ZH
        </a>
    </div>
</div>

<div class="features-grid">
    <div class="feature-card">
        <div class="feature-icon"><i class="fa-solid fa-bolt"></i></div>
        <h3 class="feature-title">{{ __('messages.feature_1_title') }}</h3>
        <p class="feature-desc">{{ __('messages.feature_1_desc') }}</p>
    </div>
    <div class="feature-card">
        <div class="feature-icon"><i class="fa-solid fa-list-check"></i></div>
        <h3 class="feature-title">{{ __('messages.feature_2_title') }}</h3>
        <p class="feature-desc">{{ __('messages.feature_2_desc') }}</p>
    </div>
    <div class="feature-card">
        <div class="feature-icon"><i class="fa-solid fa-shield-halved"></i></div>
        <h3 class="feature-title">{{ __('messages.feature_3_title') }}</h3>
        <p class="feature-desc">{{ __('messages.feature_3_desc') }}</p>
    </div>
    <div class="feature-card">
        <div class="feature-icon"><i class="fa-solid fa-users-gear"></i></div>
        <h3 class="feature-title">{{ __('messages.feature_4_title') }}</h3>
        <p class="feature-desc">{{ __('messages.feature_4_desc') }}</p>
    </div>
</div>
@endsection
