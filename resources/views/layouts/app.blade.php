<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ in_array(app()->getLocale(), ['ar']) ? 'rtl' : 'ltr' }}" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- Multilingual SEO & Canonical Meta Tags -->
    @php
        $currentLocale = app()->getLocale();
        $seoLocales = [
            'id' => 'id_ID',
            'en' => 'en_GB',
            'ar' => 'ar_SA',
            'zh' => 'zh_CN',
        ];
        $ogLocale = $seoLocales[$currentLocale] ?? 'id_ID';
        $canonicalUrl = url()->current();
    @endphp

    <link rel="canonical" href="{{ $canonicalUrl }}">

    <!-- Multi-Language Alternate Hreflang Tags for Search Engines (Google, Bing, Yandex, Baidu) -->
    <link rel="alternate" hreflang="id" href="{{ $canonicalUrl }}?lang=id">
    <link rel="alternate" hreflang="en" href="{{ $canonicalUrl }}?lang=en">
    <link rel="alternate" hreflang="ar" href="{{ $canonicalUrl }}?lang=ar">
    <link rel="alternate" hreflang="zh" href="{{ $canonicalUrl }}?lang=zh">
    <link rel="alternate" hreflang="x-default" href="{{ $canonicalUrl }}">

    <!-- Regional & Search Engine Compatibility Tags -->
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">

    <meta name="description" content="@yield('meta_description', __('messages.meta_description'))">
    <meta name="keywords" content="@yield('meta_keywords', __('messages.meta_keywords'))">
    <meta name="author" content="Hamba Allah">
    <meta name="robots" content="index, follow">

    <!-- Open Graph / Facebook / WhatsApp -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ $canonicalUrl }}">
    <meta property="og:locale" content="{{ $ogLocale }}">
    @php
        foreach($seoLocales as $locCode => $ogCode) {
            if ($locCode !== $currentLocale) {
                echo '    <meta property="og:locale:alternate" content="' . e($ogCode) . '">' . PHP_EOL;
            }
        }
    @endphp
    <meta property="og:title" content="@yield('title', __('messages.meta_title'))">
    <meta property="og:description" content="@yield('meta_description', __('messages.meta_description'))">
    <meta property="og:image" content="{{ asset('favicon.svg') }}">

    <!-- Twitter Card -->
    <meta property="twitter:card" content="summary">
    <meta property="twitter:url" content="{{ $canonicalUrl }}">
    <meta property="twitter:title" content="@yield('title', __('messages.meta_title'))">
    <meta property="twitter:description" content="@yield('meta_description', __('messages.meta_description'))">
    <meta property="twitter:image" content="{{ asset('favicon.svg') }}">

    <!-- JSON-LD Structured Data for Multilingual Search Engines -->
    <script type="application/ld+json">
    {!! json_encode([
        '@context' => 'https://schema.org',
        '@type' => 'WebApplication',
        'name' => __('messages.brand_name'),
        'url' => url('/'),
        'inLanguage' => ['id', 'en', 'ar', 'zh'],
        'description' => __('messages.meta_description'),
        'applicationCategory' => 'EducationalApplication',
        'operatingSystem' => 'All',
        'offers' => [
            '@type' => 'Offer',
            'price' => '0',
            'priceCurrency' => 'IDR',
        ],
    ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!}
    </script>

    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="shortcut icon" href="{{ asset('favicon.svg') }}">
    @vite('resources/js/app.js')
    <title>@yield('title', __('messages.meta_title'))</title>
    <style>
        @font-face {
            font-family: 'KFGQPC Uthman Taha Naskh';
            src: url("{{ asset('vendor/KFGQPC Uthman Taha Naskh Regular.ttf') }}") format('truetype');
            font-weight: normal;
            font-style: normal;
            font-display: swap;
            unicode-range: U+0600-06FF, U+0750-077F, U+08A0-08FF, U+FB50-FDFF, U+FE70-FEFF;
        }

        :root {
            /* Warm Light Mode Palette (Default) */
            --bg-body: #FAF7F5;
            --bg-card: #FFFFFF;
            --bg-card-hover: #F1F5F9;
            --text-main: #1A1A1A;
            --text-muted: #475569;
            --primary: #312E81;
            --primary-hover: #1E1B4B;
            --accent: #6366F1;
            --danger: #DC2626;
            --warning: #F59E0B;
            --border-color: #CBD5E1;
            
            /* Specific Exam Status Palette */
            --status-answered: #16A34A;
            --status-unanswered: #E2E8F0;
            --status-flagged: #F59E0B;
            --status-active: #2563EB;
            --status-timer: #DC2626;

            --font-family: 'KFGQPC Uthman Taha Naskh', system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Noto Sans CJK SC', 'Noto Sans CJK JP', 'Microsoft YaHei', 'Meiryo', 'Amiri', 'Traditional Arabic', sans-serif;
        }

        /* Multilingual RTL Support */
        [dir="rtl"] { text-align: right; direction: rtl; font-family: 'KFGQPC Uthman Taha Naskh', 'Amiri', serif; line-height: 2; }
        [dir="auto"] { text-align: start; }

        [data-theme="dark"] {
            /* Charcoal Dark Mode Palette */
            --bg-body: #1E293B;
            --bg-card: #0F172A;
            --bg-card-hover: #334155;
            --text-main: #E2E8F0;
            --text-muted: #94A3B8;
            --primary: #6366F1;
            --primary-hover: #4F46E5;
            --accent: #818CF8;
            --danger: #EF4444;
            --warning: #F59E0B;
            --border-color: #334155;
            
            --status-answered: #16A34A;
            --status-unanswered: #334155;
            --status-flagged: #F59E0B;
            --status-active: #3B82F6;
            --status-timer: #EF4444;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            background-color: var(--bg-body);
            color: var(--text-main);
            font-family: var(--font-family);
            line-height: 1.6;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        header {
            background-color: var(--bg-card);
            border-bottom: 1px solid var(--border-color);
            position: sticky;
            top: 0;
            z-index: 100;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        }

        .navbar {
            max-width: 1200px;
            margin: 0 auto;
            padding: 1rem 1.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .brand {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--primary);
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .brand span {
            color: var(--text-main);
        }

        .nav-links {
            display: flex;
            align-items: center;
            gap: 1.25rem;
            list-style: none;
        }

        .nav-links a {
            color: var(--text-muted);
            text-decoration: none;
            font-size: 0.95rem;
            font-weight: 500;
            transition: color 0.2s ease;
        }

        .nav-links a:hover, .nav-links a.active {
            color: var(--primary);
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0.6rem 1.2rem;
            font-size: 0.9rem;
            font-weight: 600;
            border-radius: 0.5rem;
            border: none;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.2s ease;
        }

        .btn-primary, .nav-links a.btn-primary {
            background-color: var(--primary);
            color: #ffffff !important;
        }

        .btn-primary:hover, .nav-links a.btn-primary:hover {
            background-color: var(--primary-hover);
            color: #ffffff !important;
        }

        .btn-accent {
            background-color: var(--accent);
            color: #ffffff;
        }

        .btn-danger {
            background-color: var(--danger);
            color: #ffffff;
        }

        .btn-secondary {
            background-color: var(--bg-card-hover);
            color: var(--text-main);
            border: 1px solid var(--border-color);
        }

        .theme-toggle-btn {
            background: transparent;
            border: 1px solid var(--border-color);
            color: var(--text-main);
            padding: 0.4rem 0.75rem;
            border-radius: 0.5rem;
            cursor: pointer;
            font-size: 0.85rem;
            display: flex;
            align-items: center;
            gap: 0.4rem;
        }

        .lang-dropdown {
            position: relative;
            display: inline-block;
        }

        .lang-dropdown-btn {
            background: transparent;
            border: 1px solid var(--border-color);
            color: var(--text-main);
            padding: 0.4rem 0.75rem;
            border-radius: 0.5rem;
            cursor: pointer;
            font-size: 0.85rem;
            display: flex;
            align-items: center;
            gap: 0.45rem;
            transition: all 0.2s ease;
        }

        .lang-dropdown-btn:hover {
            border-color: var(--primary);
            background: var(--bg-card-hover);
        }

        .lang-dropdown-menu {
            display: none;
            position: absolute;
            right: 0;
            top: calc(100% + 0.4rem);
            background-color: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 0.6rem;
            min-width: 190px;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.15), 0 8px 10px -6px rgba(0, 0, 0, 0.1);
            z-index: 1000;
            padding: 0.4rem 0;
            backdrop-filter: blur(8px);
        }

        [dir="rtl"] .lang-dropdown-menu {
            right: auto;
            left: 0;
        }

        .lang-dropdown-menu.show {
            display: block;
            animation: dropdownFadeIn 0.15s ease-out;
        }

        @keyframes dropdownFadeIn {
            from { opacity: 0; transform: translateY(-6px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .lang-dropdown-item {
            display: flex;
            align-items: center;
            gap: 0.6rem;
            padding: 0.55rem 0.9rem;
            color: var(--text-main) !important;
            text-decoration: none;
            font-size: 0.85rem;
            font-weight: 500;
            transition: all 0.15s ease;
        }

        .lang-dropdown-item:hover {
            background-color: var(--bg-card-hover);
            color: var(--primary) !important;
            padding-left: 1.1rem;
        }

        [dir="rtl"] .lang-dropdown-item:hover {
            padding-left: 0.9rem;
            padding-right: 1.1rem;
        }

        .lang-dropdown-item.active {
            font-weight: 700;
            background-color: rgba(99, 102, 241, 0.12);
            color: var(--primary) !important;
        }

        .flag-icon-tag {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 1.25rem;
            height: 1.25rem;
            font-size: 1rem;
        }

        .flag-icon-tag .fi {
            width: 1.3333em;
            line-height: 1em;
        }

        .container {
            max-width: 1200px;
            width: 100%;
            margin: 2rem auto;
            padding: 0 1.5rem;
            flex: 1;
        }

        .card {
            background-color: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 0.75rem;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.04);
        }

        .card-header {
            font-size: 1.2rem;
            font-weight: 600;
            margin-bottom: 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            justify-content: space-between;
            align-items: center;
            color: var(--text-main);
        }

        .grid-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 1.25rem;
            margin-bottom: 1.5rem;
        }

        .stat-box {
            background-color: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 0.75rem;
            padding: 1.25rem;
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .stat-number {
            font-size: 2rem;
            font-weight: 700;
            color: var(--primary);
        }

        .form-group {
            margin-bottom: 1.25rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.4rem;
            font-size: 0.9rem;
            color: var(--text-muted);
            font-weight: 500;
        }

        .form-control {
            width: 100%;
            padding: 0.75rem 1rem;
            background-color: var(--bg-body);
            border: 1px solid var(--border-color);
            border-radius: 0.5rem;
            color: var(--text-main);
            font-size: 0.95rem;
            outline: none;
            transition: border-color 0.2s ease;
        }

        .form-control:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 2px rgba(49, 46, 129, 0.1);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
        }

        th, td {
            padding: 0.75rem 1rem;
            text-align: left;
            border-bottom: 1px solid var(--border-color);
        }

        th {
            background-color: var(--bg-body);
            color: var(--text-muted);
            font-size: 0.85rem;
            text-transform: uppercase;
        }

        .alert {
            padding: 1rem 1.25rem;
            border-radius: 0.5rem;
            margin-bottom: 1.5rem;
            font-size: 0.95rem;
        }

        .alert-success {
            background-color: rgba(22, 163, 74, 0.1);
            border: 1px solid var(--status-answered);
            color: #15803d;
        }

        .alert-danger {
            background-color: rgba(220, 38, 38, 0.1);
            border: 1px solid var(--danger);
            color: #b91c1c;
        }

        /* Custom Glassmorphic Toast & Confirm Modal Engine (Zero External Dependencies) */
        .toast-container-wrapper {
            position: fixed;
            top: 1.25rem;
            right: 1.25rem;
            z-index: 10000;
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
            pointer-events: none;
        }
        .toast-box {
            pointer-events: auto;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.85rem 1.25rem;
            border-radius: 0.6rem;
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
            font-size: 0.95rem;
            font-weight: 500;
            color: var(--text-main);
            animation: toastSlideIn 0.35s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }
        .toast-box.success { border-left: 4px solid var(--status-answered); }
        .toast-box.error { border-left: 4px solid var(--danger); }
        .toast-box.warning { border-left: 4px solid var(--warning); }
        .toast-box.info { border-left: 4px solid var(--accent); }

        @keyframes toastSlideIn {
            from { opacity: 0; transform: translateY(-12px) scale(0.96); }
            to { opacity: 1; transform: translateY(0) scale(1); }
        }

        .confirm-overlay {
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, 0.6);
            backdrop-filter: blur(6px);
            z-index: 10000;
            display: flex;
            align-items: center;
            justify-content: center;
            animation: confirmFadeIn 0.2s ease forwards;
        }
        .confirm-card {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 0.85rem;
            padding: 1.75rem;
            width: 420px;
            max-width: 90%;
            box-shadow: 0 15px 35px rgba(0,0,0,0.25);
            text-align: center;
            color: var(--text-main);
        }
        @keyframes confirmFadeIn {
            from { opacity: 0; transform: scale(0.95); }
            to { opacity: 1; transform: scale(1); }
        }

        footer {
            border-top: 1px solid var(--border-color);
            padding: 1.5rem;
            text-align: center;
            font-size: 0.85rem;
            color: var(--text-muted);
            margin-top: auto;
            background-color: var(--bg-card);
        }

        @media (max-width: 768px) {
            .navbar {
                flex-wrap: wrap;
                padding: 0.75rem 1rem;
                gap: 0.75rem;
            }
            .brand-wrapper {
                flex-wrap: wrap;
                gap: 0.4rem !important;
            }
            .nav-links {
                gap: 0.6rem;
                flex-wrap: wrap;
            }
            .v1-legacy-btn {
                font-size: 0.7rem !important;
                padding: 0.15rem 0.4rem !important;
            }
        }
    </style>
    @yield('styles')
</head>
<body>
    <header>
        <div class="navbar">
            <div class="brand-wrapper" style="display: flex; align-items: center; gap: 0.6rem;">
                <a href="{{ url('/') }}" class="brand">
                    <i data-lucide="graduation-cap"></i> <span>{{ __('messages.brand_name') }}</span>
                </a>
                <a href="http://ajenono.wongcjdw.com" target="_blank" rel="noopener noreferrer" class="v1-legacy-btn" style="font-size: 0.75rem; color: var(--text-muted); text-decoration: underline; background: rgba(99, 102, 241, 0.1); padding: 0.2rem 0.5rem; border-radius: 0.3rem;" title="Open Ajenono V1 Legacy">
                    <i data-lucide="external-link" style="font-size: 0.65rem;"></i> {{ __('messages.legacy_version') }}
                </a>
            </div>
            <ul class="nav-links">
                @auth
                    @if(Auth::user()->role === 'admin')
                        <li><a href="{{ route('admin.dashboard') }}">{{ __('messages.dashboard') }}</a></li>
                    @elseif(Auth::user()->role === 'teacher')
                        <li><a href="{{ route('teacher.dashboard') }}">{{ __('messages.dashboard') }}</a></li>
                    @elseif(Auth::user()->role === 'student')
                        <li><a href="{{ route('student.dashboard') }}">{{ __('messages.exam_portal') }}</a></li>
                    @endif
                    <li>
                        <form action="{{ route('logout') }}" method="POST" style="display: inline;">
                            @csrf
                            <button type="submit" class="btn btn-secondary" style="padding: 0.4rem 0.8rem; font-size: 0.85rem;">{{ __('messages.logout') }} ({{ Auth::user()->name }})</button>
                        </form>
                    </li>
                @else
                    <li><a href="{{ route('login') }}">{{ __('messages.login') }}</a></li>
                    <li><a href="{{ route('register.school') }}" class="btn btn-primary">{{ __('messages.register_school') }}</a></li>
                @endauth
                
                <li class="lang-dropdown">
                    <button type="button" class="lang-dropdown-btn" id="langDropdownTrigger" onclick="toggleLangDropdown(event)" title="{{ __('messages.select_language') }}">
                        <i data-lucide="languages" style="color: var(--accent);"></i>
                        <span style="font-weight: 600;">
                            @php $curr = app()->getLocale(); @endphp
                            @if($curr === 'id')
                                <span class="fi fi-id" aria-hidden="true"></span> ID
                            @elseif($curr === 'en')
                                <span class="fi fi-gb" aria-hidden="true"></span> EN
                            @elseif($curr === 'ar')
                                <span class="fi fi-sa" aria-hidden="true"></span> AR
                            @elseif($curr === 'zh')
                                <span class="fi fi-cn" aria-hidden="true"></span> ZH
                            @else
                                {{ strtoupper($curr) }}
                            @endif
                        </span>
                        <i data-lucide="chevron-down" style="font-size: 0.65rem; opacity: 0.7;"></i>
                    </button>
                    <div class="lang-dropdown-menu" id="langDropdownMenu">
                        <a href="{{ route('set.locale', 'id') }}" class="lang-dropdown-item {{ app()->getLocale() === 'id' ? 'active' : '' }}">
                            <span class="flag-icon-tag"><span class="fi fi-id" aria-hidden="true"></span></span>
                            <span>{{ __('messages.lang_id') }}</span>
                        </a>
                        <a href="{{ route('set.locale', 'en') }}" class="lang-dropdown-item {{ app()->getLocale() === 'en' ? 'active' : '' }}">
                            <span class="flag-icon-tag"><span class="fi fi-gb" aria-hidden="true"></span></span>
                            <span>{{ __('messages.lang_en') }}</span>
                        </a>
                        <a href="{{ route('set.locale', 'ar') }}" class="lang-dropdown-item {{ app()->getLocale() === 'ar' ? 'active' : '' }}">
                            <span class="flag-icon-tag"><span class="fi fi-sa" aria-hidden="true"></span></span>
                            <span>{{ __('messages.lang_ar') }}</span>
                        </a>
                        <a href="{{ route('set.locale', 'zh') }}" class="lang-dropdown-item {{ app()->getLocale() === 'zh' ? 'active' : '' }}">
                            <span class="flag-icon-tag"><span class="fi fi-cn" aria-hidden="true"></span></span>
                            <span>{{ __('messages.lang_zh') }}</span>
                        </a>
                    </div>
                </li>

                <li>
                    <button id="themeToggle" class="theme-toggle-btn" onclick="toggleTheme()">
                        <i data-lucide="moon"></i> {{ __('messages.dark_mode') }}
                    </button>
                </li>
            </ul>
        </div>
    </header>

    <div class="container">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if(session('error') || $errors->any())
            <div class="alert alert-danger">
                @if(session('error'))
                    <div>{{ session('error') }}</div>
                @endif
                @foreach($errors->all() as $error)
                    <div>• {{ $error }}</div>
                @endforeach
            </div>
        @endif

        @yield('content')
    </div>

    <footer>
        <p>&copy; {{ date('Y') }} Open Source Online Exam Platform. Made with <i data-lucide="heart" style="color: #ef4444;"></i> by <a href="https://github.com/hambaallahdev01" target="_blank" rel="noopener noreferrer" style="color: var(--primary); text-decoration: underline;">Hamba Allah</a> &bull; Inspired by <a href="https://wongcjdw.com" target="_blank" rel="noopener noreferrer" style="color: var(--primary); text-decoration: underline;">Pak Wong</a> (Big thanks!).</p>
    </footer>

    <script>
        function applyTheme(theme) {
            document.documentElement.setAttribute('data-theme', theme);
            const btn = document.getElementById('themeToggle');
            if (btn) {
                btn.innerHTML = theme === 'dark' ? '<i data-lucide="sun"></i> Light' : '<i data-lucide="moon"></i> Dark';
            }
            localStorage.setItem('exam_theme', theme);
        }

        function toggleTheme() {
            const current = document.documentElement.getAttribute('data-theme') || 'light';
            const next = current === 'dark' ? 'light' : 'dark';
            applyTheme(next);
        }

        document.addEventListener('DOMContentLoaded', () => {
            const saved = localStorage.getItem('exam_theme') || 'light';
            applyTheme(saved);
        });

        // Language Dropdown Handler
        function toggleLangDropdown(event) {
            event.stopPropagation();
            const menu = document.getElementById('langDropdownMenu');
            if (menu) {
                menu.classList.toggle('show');
            }
        }

        document.addEventListener('click', (e) => {
            const menu = document.getElementById('langDropdownMenu');
            if (menu && !e.target.closest('.lang-dropdown')) {
                menu.classList.remove('show');
            }
        });

        // Global Glassmorphic Toast Engine (Zero External Dependencies)
        window.ExamToast = {
            show(message, type = 'info', duration = 3500) {
                let container = document.getElementById('globalToastContainer');
                if (!container) {
                    container = document.createElement('div');
                    container.id = 'globalToastContainer';
                    container.className = 'toast-container-wrapper';
                    document.body.appendChild(container);
                }

                const toast = document.createElement('div');
                toast.className = `toast-box ${type}`;
                
                let iconHtml = '<i data-lucide="info" style="color: var(--accent);"></i>';
                if (type === 'success') iconHtml = '<i data-lucide="circle-check" style="color: var(--status-answered);"></i>';
                if (type === 'error') iconHtml = '<i data-lucide="circle-x" style="color: var(--danger);"></i>';
                if (type === 'warning') iconHtml = '<i data-lucide="triangle-alert" style="color: var(--warning);"></i>';

                toast.innerHTML = iconHtml;
                const messageElement = document.createElement('span');
                messageElement.textContent = String(message);
                toast.appendChild(messageElement);
                container.appendChild(toast);

                setTimeout(() => {
                    toast.style.opacity = '0';
                    toast.style.transform = 'translateY(-10px)';
                    toast.style.transition = 'all 0.3s ease';
                    setTimeout(() => toast.remove(), 300);
                }, duration);
            },
            success(msg) { this.show(msg, 'success'); },
            error(msg) { this.show(msg, 'error'); },
            warning(msg) { this.show(msg, 'warning'); },
            info(msg) { this.show(msg, 'info'); }
        };

        // Global Glassmorphic Confirmation Modal Engine
        window.ExamConfirm = function(title, text, confirmBtnText = 'Ya, Lanjutkan') {
            return new Promise((resolve) => {
                const overlay = document.createElement('div');
                overlay.className = 'confirm-overlay';
                overlay.innerHTML = `
                    <div class="confirm-card">
                        <div style="font-size: 2.5rem; color: var(--warning); margin-bottom: 0.75rem;"><i data-lucide="triangle-alert"></i></div>
                        <h3 class="confirm-title" style="font-size: 1.2rem; font-weight: 700; margin-bottom: 0.5rem; color: var(--text-main);"></h3>
                        <p class="confirm-text" style="font-size: 0.9rem; color: var(--text-muted); margin-bottom: 1.5rem; line-height: 1.5;"></p>
                        <div style="display: flex; gap: 0.75rem; justify-content: center;">
                            <button class="btn btn-secondary" id="confirmBtnCancel" style="flex: 1;">Batal</button>
                            <button class="btn btn-danger" id="confirmBtnOk" style="flex: 1;"></button>
                        </div>
                    </div>
                `;
                overlay.querySelector('.confirm-title').textContent = String(title);
                overlay.querySelector('.confirm-text').textContent = String(text);
                overlay.querySelector('#confirmBtnOk').textContent = String(confirmBtnText);
                document.body.appendChild(overlay);

                overlay.querySelector('#confirmBtnCancel').onclick = () => {
                    overlay.remove();
                    resolve(false);
                };
                overlay.querySelector('#confirmBtnOk').onclick = () => {
                    overlay.remove();
                    resolve(true);
                };
            });
        };
    </script>

    @yield('scripts')
</body>
</html>
