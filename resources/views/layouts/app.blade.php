<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Manajemen Tugas')</title>

    {{-- Bootstrap 5 --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    {{-- Bootstrap Icons --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        :root {
            --primary: #5C6BC0;
            --primary-dark: #3949AB;
            --school: #5C6BC0;
            --work: #00BCD4;
        }
        body { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; }
        .card { border-radius: 16px; border: none; box-shadow: 0 4px 20px rgba(0,0,0,0.1); }
        .btn-primary { background: var(--primary); border-color: var(--primary); }
        .btn-primary:hover { background: var(--primary-dark); border-color: var(--primary-dark); }
        .badge-school { background: var(--school); color: #fff; }
        .badge-work   { background: var(--work);   color: #fff; }
        .navbar { background: rgba(255,255,255,0.15) !important; backdrop-filter: blur(10px); }
        .navbar-brand, .nav-link { color: #fff !important; }
        .task-card { transition: transform 0.2s; cursor: pointer; }
        .task-card:hover { transform: translateY(-2px); }
        .task-selesai { opacity: 0.6; }
        .task-selesai .task-title { text-decoration: line-through; }
    </style>

    @stack('styles')
</head>
<body>

{{-- Navbar --}}
<nav class="navbar navbar-expand-lg mb-4">
    <div class="container">
        <a class="navbar-brand fw-bold" href="{{ route('tasks.index') }}">
            <i class="bi bi-clipboard-check"></i> Manajemen Tugas
        </a>
        <div class="ms-auto d-flex align-items-center gap-3">
            <span class="text-white">Hello, {{ Auth::user()->name }}</span>
            <form method="POST" action="{{ route('logout') }}" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-outline-light btn-sm">
                    <i class="bi bi-box-arrow-right"></i> Logout
                </button>
            </form>
        </div>
    </div>
</nav>

<div class="container pb-5">

    {{-- Alert success/error --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-circle"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @yield('content')
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
</body>
</html>
