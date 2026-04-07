<div class="py-2">
    <div class="nav-section">Main</div>
    <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
        <i class="bi bi-speedometer2"></i> Dashboard
    </a>

    <div class="nav-section">Overview</div>
    <a href="{{ route('admin.profiles.index') }}" class="nav-link {{ request()->routeIs('admin.profiles.*') ? 'active' : '' }}">
        <i class="bi bi-people"></i> AF/AD Profiles
    </a>
    <a href="{{ route('admin.appointments.index') }}" class="nav-link {{ request()->routeIs('admin.appointments.*') ? 'active' : '' }}">
        <i class="bi bi-calendar3"></i> Appointments
    </a>
    <a href="{{ route('admin.claims.index') }}" class="nav-link {{ request()->routeIs('admin.claims.*') ? 'active' : '' }}">
        <i class="bi bi-file-earmark-text"></i> All Claims
    </a>
</div>
