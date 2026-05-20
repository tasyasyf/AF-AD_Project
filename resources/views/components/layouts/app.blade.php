@props(['title' => null])
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? config('app.name', 'AF/AD System') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        :root {
            --sidebar-width: 268px;
            --topbar-height: 64px;
            --portal-red: #c3121f;
            --portal-red-dark: #9f0f19;
            --portal-ink: #2c1d1d;
            --portal-muted: #765f5f;
            --portal-border: #efc4bf;
            --portal-soft: #fff7f6;
            --portal-panel: #fffdfc;
            --bs-primary: var(--portal-red);
            --bs-primary-rgb: 195, 18, 31;
            --bs-link-color: var(--portal-red);
            --bs-link-hover-color: var(--portal-red-dark);
        }

        body {
            color: var(--portal-ink);
            background: #fff;
            font-size: 0.9rem;
        }

        a { color: var(--portal-red); text-decoration: none; }
        a:hover { color: var(--portal-red-dark); text-decoration: underline; }

        .btn-primary {
            --bs-btn-bg: var(--portal-red);
            --bs-btn-border-color: var(--portal-red);
            --bs-btn-hover-bg: var(--portal-red-dark);
            --bs-btn-hover-border-color: var(--portal-red-dark);
            --bs-btn-active-bg: #870c14;
            --bs-btn-active-border-color: #870c14;
        }

        .btn-outline-primary {
            --bs-btn-color: var(--portal-red);
            --bs-btn-border-color: var(--portal-border);
            --bs-btn-hover-bg: var(--portal-red);
            --bs-btn-hover-border-color: var(--portal-red);
            --bs-btn-active-bg: var(--portal-red-dark);
            --bs-btn-active-border-color: var(--portal-red-dark);
        }

        .text-primary { color: var(--portal-red) !important; }
        .bg-primary { background-color: var(--portal-red) !important; }
        .border-primary { border-color: var(--portal-red) !important; }

        /* Sidebar */
        #sidebar {
            position: fixed; top: 0; left: 0; width: var(--sidebar-width);
            height: 100vh;
            background: linear-gradient(180deg, #a90f1a 0%, #7d0b13 100%);
            color: #fff;
            overflow-y: auto; z-index: 1040; transition: transform 0.3s ease;
            border-right: 1px solid rgba(255,255,255,0.16);
        }
        #sidebar .brand { padding: 1.15rem 1.35rem; border-bottom: 1px solid rgba(255,255,255,0.16); }
        #sidebar .brand-lockup {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        #sidebar .brand-logo {
            width: 64px;
            height: 34px;
            object-fit: contain;
            border-radius: 4px;
            background: #fff;
            padding: 0.22rem 0.32rem;
            flex: 0 0 auto;
        }
        #sidebar .brand h5 { margin: 0; font-weight: 800; letter-spacing: 0; font-size: 1rem; line-height: 1.15; }
        #sidebar .brand small { display: block; opacity: 0.78; font-size: 0.75rem; margin-top: 0.2rem; }
        #sidebar .nav-section { padding: 0.75rem 1rem 0.25rem; font-size: 0.7rem; text-transform: uppercase;
            letter-spacing: 1px; opacity: 0.58; }
        #sidebar .nav-link { color: rgba(255,255,255,0.78); padding: 0.65rem 1.5rem; border-radius: 0;
            display: flex; align-items: center; gap: 0.6rem; transition: all 0.2s; }
        #sidebar .nav-link:hover, #sidebar .nav-link.active {
            background: rgba(255,255,255,0.15); color: #fff; text-decoration: none; }
        #sidebar .nav-link i { width: 18px; text-align: center; }

        /* Main content */
        #main-content { margin-left: var(--sidebar-width); min-height: 100vh; background: #fff; }
        #topbar { height: var(--topbar-height); background: #fff;
            border-bottom: 1px solid var(--portal-border); padding: 0 1.5rem;
            display: flex; align-items: center; justify-content: space-between;
            position: sticky; top: 0; z-index: 1030; }
        #topbar .fw-semibold { color: var(--portal-ink); }
        .topbar-avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            object-fit: cover;
            border: 1px solid var(--portal-border);
            background: var(--portal-soft);
            color: var(--portal-red);
            flex: 0 0 36px;
        }
        .topbar-avatar-placeholder {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 1.05rem;
        }
        .profile-photo-lg {
            width: 132px;
            height: 132px;
            border-radius: 50%;
            object-fit: cover;
            border: 1px solid var(--portal-border);
            background: var(--portal-soft);
            color: var(--portal-red);
        }
        .profile-photo-placeholder {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 3.2rem;
        }
        .page-content { padding: 1.75rem; }

        /* Cards */
        .card {
            border: 1px solid var(--portal-border);
            border-radius: 6px;
            background: #fff;
            box-shadow: 0 10px 24px rgba(139, 31, 31, 0.05);
        }
        .card-header {
            border-bottom-color: #f2d5d1;
            background: #fff9f8 !important;
            color: var(--portal-ink);
        }
        .stat-card { border-left: 4px solid var(--portal-red) !important; }
        .stat-card .card-body {
            min-height: 92px;
            padding: 1rem 1.1rem;
        }
        .stat-card .rounded-circle {
            width: 50px !important;
            height: 50px !important;
            flex: 0 0 50px;
            border: 1px solid rgba(195, 18, 31, 0.18);
            background-color: rgba(195, 18, 31, 0.08) !important;
            color: var(--portal-red);
        }
        .stat-card .rounded-circle i {
            color: var(--portal-red) !important;
            font-size: 1.25rem !important;
        }
        .stat-card .text-muted.small {
            display: block;
            margin-bottom: 0.35rem;
            color: var(--portal-muted) !important;
            font-size: 0.78rem;
            font-weight: 600;
        }
        .stat-card .fw-bold.fs-4 {
            font-size: 1.55rem !important;
            line-height: 1.1;
            color: var(--portal-ink);
        }
        .stat-card-link {
            display: block;
            height: 100%;
            color: inherit;
            text-decoration: none;
        }
        .stat-card-link:hover,
        .stat-card-link:focus {
            color: inherit;
            text-decoration: none;
        }
        .stat-card-link .stat-card {
            transition: transform 0.16s ease, box-shadow 0.16s ease, border-color 0.16s ease;
        }
        .stat-card-link:hover .stat-card,
        .stat-card-link:focus .stat-card {
            transform: translateY(-2px);
            border-color: var(--portal-red);
            box-shadow: 0 14px 30px rgba(139, 31, 31, 0.12);
        }
        .stat-card-link:focus-visible {
            border-radius: 6px;
            outline: 3px solid rgba(195, 18, 31, 0.22);
            outline-offset: 3px;
        }

        .table {
            --bs-table-color: var(--portal-ink);
            --bs-table-hover-bg: #fff4f2;
            border-color: #f2d5d1;
        }
        .table-light {
            --bs-table-bg: #fff4f2;
            --bs-table-color: #523636;
            border-color: #f2d5d1;
        }
        .table thead th {
            font-size: 0.75rem;
            letter-spacing: 0;
            text-transform: uppercase;
            color: #6a4c4c;
        }

        .form-control,
        .form-select,
        .input-group-text {
            border-color: var(--portal-border);
        }
        .form-control:focus,
        .form-select:focus {
            border-color: var(--portal-red);
            box-shadow: 0 0 0 0.2rem rgba(195, 18, 31, 0.12);
        }

        .badge.bg-primary {
            background-color: var(--portal-red) !important;
        }

        @media (max-width: 768px) {
            #sidebar { transform: translateX(-100%); }
            #sidebar.show { transform: translateX(0); }
            #main-content { margin-left: 0; }
            #topbar { padding: 0 1rem; }
            .page-content { padding: 1rem; }
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <nav id="sidebar">
        <div class="brand">
            <div class="brand-lockup">
                <img src="{{ asset('images/aeu-logo.svg') }}" alt="AEU" class="brand-logo">
                <div>
                    <h5>{{ auth()->user()->name }}</h5>
                    <small>
                        @if(auth()->user()->isAfAd()) AF/AD
                        @elseif(auth()->user()->isExecutive()) School Executive
                        @elseif(auth()->user()->isProgramCoordinator()) Program Coordinator
                        @else Administrator
                        @endif
                    </small>
                </div>
            </div>
        </div>

        @if(auth()->user()->isAfAd())
            @include('layouts.partials.nav-afad')
        @elseif(auth()->user()->isExecutive())
            @include('layouts.partials.nav-executive')
        @elseif(auth()->user()->isProgramCoordinator())
            @include('layouts.partials.nav-pc')
        @else
            @include('layouts.partials.nav-admin')
        @endif
    </nav>

    <!-- Main content -->
    <div id="main-content">
        <!-- Topbar -->
        <div id="topbar">
            <div class="d-flex align-items-center gap-2">
                <button class="btn btn-sm btn-light d-md-none" onclick="document.getElementById('sidebar').classList.toggle('show')">
                    <i class="bi bi-list"></i>
                </button>
                <span class="text-muted fw-semibold">{{ $title ?? '' }}</span>
            </div>
            <div class="d-flex align-items-center gap-3">
                @if(!auth()->user()->isAdmin())
                    @if(auth()->user()->profile_photo_path)
                        <img src="{{ route('profile-photo.show', auth()->user()) }}" alt="{{ auth()->user()->name }}" class="topbar-avatar">
                    @else
                        <span class="topbar-avatar topbar-avatar-placeholder" aria-label="No profile photo">
                            <i class="bi bi-person"></i>
                        </span>
                    @endif
                @endif
                <span class="text-muted small">{{ auth()->user()->name }}</span>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-outline-danger">
                        <i class="bi bi-box-arrow-right"></i> Logout
                    </button>
                </form>
            </div>
        </div>

        <!-- Page content -->
        <div class="page-content">
            <x-alert />
            {{ $slot }}
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
