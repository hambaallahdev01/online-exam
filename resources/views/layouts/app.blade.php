<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Exam System') - Open Source Online Exam Platform</title>
    <style>
        :root {
            --bg-body: #0f172a;
            --bg-card: #1e293b;
            --bg-card-hover: #334155;
            --text-main: #f8fafc;
            --text-muted: #94a3b8;
            --primary: #6366f1;
            --primary-hover: #4f46e5;
            --accent: #10b981;
            --danger: #ef4444;
            --warning: #f59e0b;
            --border-color: #334155;
            --font-family: system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
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
        }

        header {
            background-color: rgba(30, 41, 59, 0.8);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid var(--border-color);
            position: sticky;
            top: 0;
            z-index: 100;
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
            color: #ffffff;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .brand span {
            background: linear-gradient(135deg, #6366f1, #10b981);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
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
            transition: color 0.2s ease;
        }

        .nav-links a:hover, .nav-links a.active {
            color: #ffffff;
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

        .btn-primary {
            background-color: var(--primary);
            color: #ffffff;
        }

        .btn-primary:hover {
            background-color: var(--primary-hover);
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
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
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
            background-color: rgba(15, 23, 42, 0.5);
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
            background-color: rgba(16, 185, 129, 0.15);
            border: 1px solid var(--accent);
            color: #34d399;
        }

        .alert-danger {
            background-color: rgba(239, 68, 68, 0.15);
            border: 1px solid var(--danger);
            color: #f87171;
        }

        footer {
            border-top: 1px solid var(--border-color);
            padding: 1.5rem;
            text-align: center;
            font-size: 0.85rem;
            color: var(--text-muted);
            margin-top: auto;
        }
    </style>
    @yield('styles')
</head>
<body>
    <header>
        <div class="navbar">
            <a href="{{ url('/') }}" class="brand">
                ⚡ <span>ExamPlatform</span>
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
        <p>&copy; {{ date('Y') }} Open Source Online Exam Platform. Built with Laravel 13 & Vanilla JS.</p>
    </footer>

    @yield('scripts')
</body>
</html>
