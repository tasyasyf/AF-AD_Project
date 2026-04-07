<div class="py-2">
    <div class="nav-section">Main</div>
    <a href="{{ route('executive.dashboard') }}" class="nav-link {{ request()->routeIs('executive.dashboard') ? 'active' : '' }}">
        <i class="bi bi-speedometer2"></i> Dashboard
    </a>

    <div class="nav-section">Profiles</div>
    <a href="{{ route('executive.profiles.index') }}" class="nav-link {{ request()->routeIs('executive.profiles.*') ? 'active' : '' }}">
        <i class="bi bi-people"></i> AF/AD Profiles
    </a>

    <div class="nav-section">Appointments</div>
    <a href="{{ route('executive.appointments.index') }}" class="nav-link {{ request()->routeIs('executive.appointments.*') ? 'active' : '' }}">
        <i class="bi bi-calendar3"></i> Appointments
    </a>
    <a href="{{ route('executive.appointments.create') }}" class="nav-link">
        <i class="bi bi-plus-circle"></i> New Appointment
    </a>

    <div class="nav-section">Claims</div>
    <a href="{{ route('executive.claims.index') }}" class="nav-link {{ request()->routeIs('executive.claims.*') ? 'active' : '' }}">
        <i class="bi bi-file-earmark-check"></i> Review Claims
    </a>
</div>
