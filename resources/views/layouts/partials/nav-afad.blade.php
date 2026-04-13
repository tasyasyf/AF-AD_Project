<div class="py-2">
    <div class="nav-section">Main</div>
    <a href="{{ route('afad.dashboard') }}" class="nav-link {{ request()->routeIs('afad.dashboard') ? 'active' : '' }}">
        <i class="bi bi-speedometer2"></i> Dashboard
    </a>

    <div class="nav-section">My Profile</div>
    <a href="{{ route('afad.profile.show') }}" class="nav-link {{ request()->routeIs('afad.profile.*') ? 'active' : '' }}">
        <i class="bi bi-person-badge"></i> Profile
    </a>
    <a href="{{ route('afad.certificates.index') }}" class="nav-link {{ request()->routeIs('afad.certificates.*') ? 'active' : '' }}">
        <i class="bi bi-award"></i> Certificates
    </a>

    <div class="nav-section">Classes</div>
    <a href="{{ route('afad.classes.index') }}" class="nav-link {{ request()->routeIs('afad.classes.*') ? 'active' : '' }}">
        <i class="bi bi-journal-bookmark"></i> My Classes
    </a>
    <a href="{{ route('afad.classes.create') }}" class="nav-link">
        <i class="bi bi-plus-circle"></i> Add Class
    </a>

    <div class="nav-section">Appointments</div>
    <a href="{{ route('afad.appointments.index') }}" class="nav-link {{ request()->routeIs('afad.appointments.*') ? 'active' : '' }}">
        <i class="bi bi-calendar3"></i> My Appointments
    </a>

    <div class="nav-section">Claims</div>
    <a href="{{ route('afad.claims.index') }}" class="nav-link {{ request()->routeIs('afad.claims.*') ? 'active' : '' }}">
        <i class="bi bi-file-earmark-text"></i> My Claims
    </a>
    <a href="{{ route('afad.claims.create') }}" class="nav-link">
        <i class="bi bi-plus-circle"></i> New Claim
    </a>
</div>
