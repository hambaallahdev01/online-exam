<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="{{ asset('vendor/fontawesome/css/all.min.css') }}">
    <title>@yield('title', 'Exam System') - Open Source Online Exam Platform</title>
    <style>
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

            --font-family: system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
        }

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

        footer {
            border-top: 1px solid var(--border-color);
            padding: 1.5rem;
            text-align: center;
            font-size: 0.85rem;
            color: var(--text-muted);
            margin-top: auto;
            background-color: var(--bg-card);
        }
    </style>
    @yield('styles')
</head>
<body>
    <header>
        <div class="navbar">
            <a href="{{ url('/') }}" class="brand">
                <i class="fa-solid fa-graduation-cap"></i> <span>ExamPlatform</span>
            </a>
            <ul class="nav-links">
                @auth
                    @if(Auth::user()->isAdmin())
                        <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li><a href="{{ route('admin.teachers') }}">Teachers</a></li>
                        <li><a href="{{ route('admin.students') }}">Students</a></li>
                    @elseif(Auth::user()->isTeacher())
                        <li><a href="{{ route('teacher.dashboard') }}">Dashboard</a></li>
                    @else
                        <li><a href="{{ route('student.dashboard') }}">Exam Portal</a></li>
                    @endif
                    <li>
                        <form action="{{ route('logout') }}" method="POST" style="display: inline;">
                            @csrf
                            <button type="submit" class="btn btn-secondary" style="padding: 0.4rem 0.8rem; font-size: 0.85rem;">Logout ({{ Auth::user()->name }})</button>
                        </form>
                    </li>
                @else
                    <li><a href="{{ route('login') }}">Login</a></li>
                    <li><a href="{{ route('register.school') }}" class="btn btn-primary">Register School</a></li>
                @endauth

                <li>
                    <button id="themeToggle" class="theme-toggle-btn" onclick="toggleTheme()">
                        🌙 Dark
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
                @if(session('error')) {{ session('error') }} @endif
                @foreach($errors->all() as $error)
                    <div>• {{ $error }}</div>
                @endforeach
            </div>
        @endif

        @yield('content')
    </div>

    <footer>
        <p>&copy; {{ date('Y') }} Open Source Online Exam Platform. Made with <i class="fa-solid fa-heart" style="color: #ef4444;"></i> by <a href="https://github.com/animfahmy" target="_blank" rel="noopener noreferrer" style="color: var(--primary); text-decoration: underline;">Achmad An'im</a>.</p>
    </footer>

    <script>
        function applyTheme(theme) {
            document.documentElement.setAttribute('data-theme', theme);
            const btn = document.getElementById('themeToggle');
            if (btn) {
                btn.innerHTML = theme === 'dark' ? '<i class="fa-solid fa-sun"></i> Light' : '<i class="fa-solid fa-moon"></i> Dark';
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
    </script>

    @yield('scripts')
</body>
</html>
