<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name') }} — @yield('title', 'Dashboard')</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;500;600;700;800&family=IBM+Plex+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <style>
        :root {
            --navy:       #0D2137;
            --navy-dark:  #071526;
            --navy-mid:   #1A3A5C;
            --amber:      #E8A020;
            --amber-light:#FEF3C7;
            --surface:    #F8FAFC;
            --border:     #E2E8F0;
            --text-main:  #1E293B;
            --text-muted: #64748B;
            --sidebar-w:  260px;
            --font-head:  'Sora', sans-serif;
            --font-body:  'IBM Plex Sans', sans-serif;
        }

        *, *::before, *::after { box-sizing: border-box; margin: 0; }
        body {
            font-family: var(--font-body);
            background: var(--surface);
            color: var(--text-main);
            font-size: 14px;
        }

        /* ── SIDEBAR ── */
        .sidebar {
            position: fixed; top: 0; left: 0;
            width: var(--sidebar-w);
            height: 100vh;
            background: var(--navy-dark);
            display: flex; flex-direction: column;
            z-index: 1000;
            border-right: 1px solid rgba(255,255,255,.06);
        }
        .sidebar-brand {
            padding: 24px 20px 20px;
            border-bottom: 1px solid rgba(255,255,255,.08);
        }
        .sidebar-brand .brand-icon {
            width: 38px; height: 38px;
            background: var(--amber);
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            font-size: 18px; color: var(--navy-dark); font-weight: 800;
            font-family: var(--font-head);
        }
        .brand-name {
            font-family: var(--font-head);
            font-size: 13px; font-weight: 700;
            color: #fff; letter-spacing: .5px;
        }
        .brand-tagline { font-size: 10px; color: rgba(255,255,255,.4); }

        .sidebar-nav { flex: 1; overflow-y: auto; padding: 16px 12px; }
        .nav-section-label {
            font-size: 10px; font-weight: 600; letter-spacing: 1.2px;
            color: rgba(255,255,255,.3); text-transform: uppercase;
            padding: 16px 8px 8px;
        }
        .nav-link-item {
            display: flex; align-items: center; gap: 10px;
            padding: 10px 12px; border-radius: 8px;
            color: rgba(255,255,255,.65);
            text-decoration: none; font-size: 13.5px;
            transition: background .15s, color .15s;
            margin-bottom: 2px;
        }
        .nav-link-item:hover,
        .nav-link-item.active {
            background: rgba(255,255,255,.08);
            color: #fff;
        }
        .nav-link-item.active { background: rgba(232,160,32,.15); color: var(--amber); }
        .nav-link-item i { font-size: 16px; width: 20px; text-align: center; }

        .sidebar-footer {
            padding: 16px 12px;
            border-top: 1px solid rgba(255,255,255,.08);
        }
        .user-card {
            display: flex; align-items: center; gap: 10px;
            padding: 10px; border-radius: 8px;
            background: rgba(255,255,255,.05);
        }
        .user-avatar {
            width: 34px; height: 34px; border-radius: 50%;
            background: var(--amber); color: var(--navy-dark);
            display: flex; align-items: center; justify-content: center;
            font-weight: 700; font-size: 13px;
        }
        .user-name { font-size: 12.5px; color: #fff; font-weight: 600; }
        .user-role { font-size: 11px; color: rgba(255,255,255,.4); }

        /* ── MAIN LAYOUT ── */
        .main-wrapper {
            margin-left: var(--sidebar-w);
            min-height: 100vh;
            display: flex; flex-direction: column;
        }

        /* ── TOPBAR ── */
        .topbar {
            background: #fff;
            border-bottom: 1px solid var(--border);
            padding: 0 28px;
            height: 60px;
            display: flex; align-items: center; justify-content: space-between;
            position: sticky; top: 0; z-index: 100;
        }
        .page-title {
            font-family: var(--font-head);
            font-size: 16px; font-weight: 700;
            color: var(--navy);
        }
        .topbar-actions { display: flex; align-items: center; gap: 12px; }

        /* ── CONTENT ── */
        .content { padding: 28px; flex: 1; }

        /* ── STAT CARDS ── */
        .stat-card {
            background: #fff;
            border: 1px solid var(--border);
            border-radius: 14px;
            padding: 22px 24px;
            position: relative; overflow: hidden;
        }
        .stat-card::before {
            content: ''; position: absolute;
            top: 0; left: 0; right: 0;
            height: 3px; background: var(--amber);
        }
        .stat-card .stat-icon {
            width: 44px; height: 44px;
            background: var(--amber-light);
            border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            font-size: 20px; color: var(--amber);
        }
        .stat-value {
            font-family: var(--font-head);
            font-size: 28px; font-weight: 800;
            color: var(--navy);
        }
        .stat-label { font-size: 12.5px; color: var(--text-muted); font-weight: 500; }

        /* ── CARDS ── */
        .card-modern {
            background: #fff;
            border: 1px solid var(--border);
            border-radius: 14px;
            overflow: hidden;
        }
        .card-modern-header {
            padding: 18px 24px;
            border-bottom: 1px solid var(--border);
            display: flex; align-items: center; justify-content: space-between;
        }
        .card-modern-header h6 {
            font-family: var(--font-head);
            font-size: 14px; font-weight: 700; color: var(--navy);
        }
        .card-modern-body { padding: 24px; }

        /* ── TABLES ── */
        .table-modern { width: 100%; border-collapse: collapse; }
        .table-modern th {
            font-size: 11px; font-weight: 700; letter-spacing: .8px;
            text-transform: uppercase; color: var(--text-muted);
            padding: 12px 16px; background: var(--surface);
            border-bottom: 2px solid var(--border);
        }
        .table-modern td {
            padding: 14px 16px;
            border-bottom: 1px solid var(--border);
            font-size: 13.5px; color: var(--text-main);
            vertical-align: middle;
        }
        .table-modern tbody tr:hover { background: var(--surface); }

        /* ── BADGES ── */
        .badge-verified   { background: #DCFCE7; color: #166534; }
        .badge-pending    { background: #FEF9C3; color: #854D0E; }
        .badge-rejected   { background: #FEE2E2; color: #991B1B; }
        .badge-suspended  { background: #F1F5F9; color: #475569; }
        .badge-active     { background: #DCFCE7; color: #166534; }
        .badge-closed     { background: #F1F5F9; color: #475569; }
        .badge-clean      { background: #DCFCE7; color: #166534; }
        .badge-blacklisted{ background: #FEE2E2; color: #991B1B; }
        .badge-nfer {
            display: inline-flex; align-items: center; gap: 4px;
            padding: 4px 10px; border-radius: 20px; font-size: 11px; font-weight: 600;
        }

        /* ── VERIFY BADGE ── */
        .verified-shield {
            display: inline-flex; align-items: center; gap: 6px;
            background: var(--amber); color: var(--navy-dark);
            padding: 4px 12px; border-radius: 20px;
            font-size: 11px; font-weight: 700;
        }

        /* ── FORM CONTROLS ── */
        .form-control, .form-select {
            border: 1.5px solid var(--border);
            border-radius: 8px; font-size: 13.5px;
            padding: 10px 14px; transition: border-color .2s;
        }
        .form-control:focus, .form-select:focus {
            border-color: var(--navy-mid); box-shadow: none;
        }
        .form-label { font-size: 12.5px; font-weight: 600; color: var(--text-main); }

        /* ── BUTTONS ── */
        .btn-navy {
            background: var(--navy); color: #fff; border: none;
            padding: 10px 20px; border-radius: 8px; font-size: 13.5px; font-weight: 600;
            transition: background .2s;
        }
        .btn-navy:hover { background: var(--navy-mid); color: #fff; }
        .btn-amber {
            background: var(--amber); color: var(--navy-dark); border: none;
            padding: 10px 20px; border-radius: 8px; font-size: 13.5px; font-weight: 700;
        }
        .btn-amber:hover { background: #d4901a; color: var(--navy-dark); }
        .btn-outline-navy {
            background: transparent; color: var(--navy);
            border: 1.5px solid var(--navy);
            padding: 9px 18px; border-radius: 8px; font-size: 13px; font-weight: 600;
        }

        /* ── ALERTS ── */
        .alert-success-custom {
            background: #F0FDF4; border: 1px solid #86EFAC;
            color: #166534; border-radius: 10px; padding: 12px 16px; font-size: 13.5px;
        }
        .alert-danger-custom {
            background: #FEF2F2; border: 1px solid #FECACA;
            color: #991B1B; border-radius: 10px; padding: 12px 16px; font-size: 13.5px;
        }

        /* ── TIMELINE ── */
        .timeline { position: relative; padding-left: 28px; }
        .timeline::before {
            content: ''; position: absolute; left: 8px; top: 0; bottom: 0;
            width: 2px; background: var(--border);
        }
        .timeline-item { position: relative; margin-bottom: 28px; }
        .timeline-dot {
            position: absolute; left: -24px; top: 4px;
            width: 12px; height: 12px; border-radius: 50%;
            background: var(--amber); border: 2px solid #fff;
            box-shadow: 0 0 0 2px var(--amber);
        }
        .timeline-dot.closed { background: var(--text-muted); box-shadow: 0 0 0 2px var(--text-muted); }

        /* ── STAR RATING ── */
        .stars { color: var(--amber); font-size: 14px; }
        .stars .empty { color: var(--border); }

        /* ── SEARCH BAR ── */
        .search-hero {
            background: linear-gradient(135deg, var(--navy-dark) 0%, var(--navy-mid) 100%);
            padding: 48px 36px;
            border-radius: 16px;
            color: #fff;
        }

        /* ── RESPONSIVE ── */
        @media (max-width: 768px) {
            .sidebar { transform: translateX(-100%); }
            .main-wrapper { margin-left: 0; }
        }
    </style>

    @stack('styles')
</head>
<body>

    {{-- SIDEBAR --}}
    <aside class="sidebar">
        <div class="sidebar-brand d-flex align-items-center gap-3">
            <div class="brand-icon">N</div>
            <div>
                <div class="brand-name">NFER-EHVS</div>
                <div class="brand-tagline">Employment Registry</div>
            </div>
        </div>

        <nav class="sidebar-nav">
            @auth
                @if(auth()->user()->isAdmin())
                    @include('layouts.partials.sidebar-admin')
                @elseif(auth()->user()->isEmployer())
                    @include('layouts.partials.sidebar-employer')
                @else
                    @include('layouts.partials.sidebar-employee')
                @endif
            @endauth
        </nav>

        <div class="sidebar-footer">
            @auth
            <div class="user-card">
                <div class="user-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
                <div class="flex-grow-1 overflow-hidden">
                    <div class="user-name text-truncate">{{ auth()->user()->name }}</div>
                    <div class="user-role">{{ ucfirst(auth()->user()->account_type) }}</div>
                </div>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-sm p-0 text-white opacity-50 border-0 bg-transparent" title="Logout">
                        <i class="bi bi-box-arrow-right fs-5"></i>
                    </button>
                </form>
            </div>
            @endauth
        </div>
    </aside>

    {{-- MAIN WRAPPER --}}
    <div class="main-wrapper">
        {{-- TOPBAR --}}
        <header class="topbar">
            <div class="page-title">@yield('page-title', 'Dashboard')</div>
            <div class="topbar-actions">
                @if(auth()->user()?->isEmployer() && auth()->user()->employer?->isVerified())
                    <a href="{{ route('employer.search.index') }}" class="btn btn-amber btn-sm">
                        <i class="bi bi-search me-1"></i> Verify Employee
                    </a>
                @endif
                <div class="text-muted small">
                    <i class="bi bi-calendar3 me-1"></i>{{ now()->format('d M Y') }}
                </div>
            </div>
        </header>

        {{-- CONTENT --}}
        <main class="content">
            {{-- Flash Messages --}}
            @if(session('success'))
                <div class="alert-success-custom mb-4 d-flex align-items-center gap-2">
                    <i class="bi bi-check-circle-fill"></i>
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error') || $errors->any())
                <div class="alert-danger-custom mb-4">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    @if(session('error')) {{ session('error') }} @endif
                    @if($errors->any())
                        <ul class="mb-0 mt-1 ps-3">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            @endif

            @yield('content')
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>