<div class="py-2">
    <div class="nav-section">Main</div>
    <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
        <i class="bi bi-speedometer2"></i> Dashboard
    </a>

    <div class="nav-section">Overview</div>
    <a href="{{ route('admin.users.index') }}" class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
        <i class="bi bi-person-gear"></i> Users & Roles
    </a>
    <a href="{{ route('admin.permissions.index') }}" class="nav-link {{ request()->routeIs('admin.permissions.*') ? 'active' : '' }}">
        <i class="bi bi-shield-check"></i> Permissions
    </a>
    <a href="{{ route('admin.profiles.index') }}" class="nav-link {{ request()->routeIs('admin.profiles.*') ? 'active' : '' }}">
        <i class="bi bi-people"></i> AF/AD Profiles
    </a>
    <a href="{{ route('admin.appointments.index') }}" class="nav-link {{ request()->routeIs('admin.appointments.*') ? 'active' : '' }}">
        <i class="bi bi-calendar3"></i> Appointments
    </a>
    <a href="{{ route('admin.classes.index') }}" class="nav-link {{ request()->routeIs('admin.classes.*') ? 'active' : '' }}">
        <i class="bi bi-journal-text"></i> Classes
    </a>
    <a href="{{ route('admin.certificates.index') }}" class="nav-link {{ request()->routeIs('admin.certificates.*') ? 'active' : '' }}">
        <i class="bi bi-award"></i> Certificates
    </a>
    <a href="{{ route('admin.submissions.index') }}" class="nav-link {{ request()->routeIs('admin.submissions.*') ? 'active' : '' }}">
        <i class="bi bi-inbox"></i> Submissions
    </a>
    <a href="{{ route('admin.claims.index') }}" class="nav-link {{ request()->routeIs('admin.claims.*') ? 'active' : '' }}">
        <i class="bi bi-file-earmark-text"></i> All Claims
    </a>
</div>
