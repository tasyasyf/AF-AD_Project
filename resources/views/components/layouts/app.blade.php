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
        :root { --sidebar-width: 260px; --topbar-height: 56px; }
        body { background-color: #f4f6f9; font-size: 0.9rem; }

        /* Sidebar */
        #sidebar {
            position: fixed; top: 0; left: 0; width: var(--sidebar-width);
            height: 100vh; background: #1a3a6e; color: #fff;
            overflow-y: auto; z-index: 1040; transition: transform 0.3s ease;
        }
        #sidebar .brand { padding: 1.2rem 1.5rem; border-bottom: 1px solid rgba(255,255,255,0.1); }
        #sidebar .brand h5 { margin: 0; font-weight: 700; letter-spacing: 1px; font-size: 1rem; }
        #sidebar .brand small { opacity: 0.7; font-size: 0.75rem; }
        #sidebar .nav-section { padding: 0.75rem 1rem 0.25rem; font-size: 0.7rem; text-transform: uppercase;
            letter-spacing: 1px; opacity: 0.5; }
        #sidebar .nav-link { color: rgba(255,255,255,0.75); padding: 0.6rem 1.5rem; border-radius: 0;
            display: flex; align-items: center; gap: 0.6rem; transition: all 0.2s; }
        #sidebar .nav-link:hover, #sidebar .nav-link.active {
            background: rgba(255,255,255,0.12); color: #fff; }
        #sidebar .nav-link i { width: 18px; text-align: center; }

        /* Main content */
        #main-content { margin-left: var(--sidebar-width); min-height: 100vh; }
        #topbar { height: var(--topbar-height); background: #fff;
            border-bottom: 1px solid #e9ecef; padding: 0 1.5rem;
            display: flex; align-items: center; justify-content: space-between;
            position: sticky; top: 0; z-index: 1030; }
        .page-content { padding: 1.5rem; }

        /* Cards */
        .card { border: none; border-radius: 10px; box-shadow: 0 1px 4px rgba(0,0,0,0.07); }
        .stat-card { border-left: 4px solid; }

        @media (max-width: 768px) {
            #sidebar { transform: translateX(-100%); }
            #sidebar.show { transform: translateX(0); }
            #main-content { margin-left: 0; }
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <nav id="sidebar">
        <div class="brand">
            <h5><i class="bi bi-mortarboard-fill me-2"></i>AF/AD System</h5>
            <small>
                @if(auth()->user()->isAfAd()) Academic Staff
                @elseif(auth()->user()->isExecutive()) School Executive
                @else Administrator
                @endif
            </small>
        </div>

        @if(auth()->user()->isAfAd())
            @include('layouts.partials.nav-afad')
        @elseif(auth()->user()->isExecutive())
            @include('layouts.partials.nav-executive')
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
