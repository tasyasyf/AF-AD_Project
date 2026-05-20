<div class="py-2">
    <div class="nav-section">Main</div>
    <a href="{{ route('pc.dashboard') }}" class="nav-link {{ request()->routeIs('pc.dashboard') ? 'active' : '' }}">
        <i class="bi bi-speedometer2"></i> Dashboard
    </a>
    <a href="{{ route('pc.profile.show') }}" class="nav-link {{ request()->routeIs('pc.profile.*') ? 'active' : '' }}">
        <i class="bi bi-person-circle"></i> My Profile
    </a>

    <div class="nav-section">Coordinator</div>
    <a href="{{ route('pc.afad.index') }}" class="nav-link {{ request()->routeIs('pc.afad.*') ? 'active' : '' }}">
        <i class="bi bi-people"></i> AF/AD List
    </a>
    <a href="{{ route('pc.appointments.index') }}" class="nav-link {{ request()->routeIs('pc.appointments.*') ? 'active' : '' }}">
        <i class="bi bi-calendar3"></i> Appointments
    </a>
    <a href="{{ route('pc.nomination.index') }}" class="nav-link {{ request()->routeIs('pc.nomination.*') ? 'active' : '' }}">
        <i class="bi bi-person-check"></i> Nomination
    </a>
    <a href="{{ route('pc.document-checklist.index') }}" class="nav-link {{ request()->routeIs('pc.document-checklist.*') ? 'active' : '' }}">
        <i class="bi bi-clipboard-check"></i> Document Checklist
    </a>
    <a href="{{ route('pc.claims.index') }}" class="nav-link {{ request()->routeIs('pc.claims.*') ? 'active' : '' }}">
        <i class="bi bi-file-earmark-check"></i> Claim Endorsement
    </a>
    <a href="{{ route('pc.reports.index') }}" class="nav-link {{ request()->routeIs('pc.reports.*') ? 'active' : '' }}">
        <i class="bi bi-bar-chart"></i> Reports
    </a>
</div>
