<x-layouts.app title="Admin Dashboard">

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <h5 class="fw-bold mb-0">System Overview</h5>
    <div class="d-flex gap-2 flex-wrap">
        <a href="{{ route('admin.profiles.create') }}" class="btn btn-primary btn-sm">
            <i class="bi bi-person-plus me-1"></i> New Profile
        </a>
        <a href="{{ route('admin.users.create') }}" class="btn btn-outline-primary btn-sm">
            <i class="bi bi-person-gear me-1"></i> New User
        </a>
        <a href="{{ route('admin.appointments.create') }}" class="btn btn-outline-primary btn-sm">
            <i class="bi bi-calendar-plus me-1"></i> New Appointment
        </a>
        <a href="{{ route('admin.claims.create') }}" class="btn btn-outline-primary btn-sm">
            <i class="bi bi-file-earmark-plus me-1"></i> New Claim
        </a>
        <a href="{{ route('admin.submissions.create') }}" class="btn btn-outline-primary btn-sm">
            <i class="bi bi-inbox me-1"></i> New Submission
        </a>
    </div>
</div>

<div class="row g-3 mb-4">
    @foreach([
        ['label'=>'Total AF/AD Users', 'value'=>$stats['total_afad'], 'icon'=>'people', 'color'=>'primary', 'href'=>route('admin.profiles.index')],
        ['label'=>'School Executives', 'value'=>$stats['total_executives'], 'icon'=>'person-workspace', 'color'=>'info', 'href'=>route('admin.users.index', ['role'=>'executive'])],
        ['label'=>'Admin Users', 'value'=>$stats['total_admins'], 'icon'=>'person-gear', 'color'=>'secondary', 'href'=>route('admin.users.index', ['role'=>'admin'])],
        ['label'=>'Registered Profiles', 'value'=>$stats['total_profiles'], 'icon'=>'person-badge', 'color'=>'info', 'href'=>route('admin.profiles.index')],
        ['label'=>'Verified Profiles', 'value'=>$stats['verified_profiles'], 'icon'=>'check-circle-fill', 'color'=>'success', 'href'=>route('admin.profiles.index', ['status'=>'verified'])],
        ['label'=>'Pending Verification', 'value'=>$stats['pending_profiles'], 'icon'=>'hourglass-split', 'color'=>'warning', 'href'=>route('admin.profiles.index', ['status'=>'pending'])],
        ['label'=>'Total Appointments', 'value'=>$stats['total_appointments'], 'icon'=>'calendar3', 'color'=>'primary', 'href'=>route('admin.appointments.index')],
        ['label'=>'Total Claims', 'value'=>$stats['total_claims'], 'icon'=>'file-earmark-text', 'color'=>'secondary', 'href'=>route('admin.claims.index')],
        ['label'=>'Approved Claims', 'value'=>$stats['claims_approved'], 'icon'=>'check2-all', 'color'=>'success', 'href'=>route('admin.claims.index', ['status'=>'approved'])],
        ['label'=>'Claims Pending Review', 'value'=>$stats['claims_pending'], 'icon'=>'clock-history', 'color'=>'warning', 'href'=>route('admin.claims.index', ['status'=>'submitted'])],
    ] as $s)
    <div class="col-sm-6 col-xl-3">
        <a href="{{ $s['href'] }}" class="stat-card-link">
            <div class="card stat-card h-100" style="border-color:var(--bs-{{ $s['color'] }})">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center bg-{{ $s['color'] }} bg-opacity-10"
                        style="width:48px;height:48px;flex-shrink:0">
                        <i class="bi bi-{{ $s['icon'] }} fs-5 text-{{ $s['color'] }}"></i>
                    </div>
                    <div>
                        <div class="text-muted small">{{ $s['label'] }}</div>
                        <div class="fw-bold fs-4">{{ $s['value'] }}</div>
                    </div>
                </div>
            </div>
        </a>
    </div>
    @endforeach
</div>

<div class="row g-3 mb-4">
    <div class="col-md-4">
        <a href="{{ route('admin.profiles.create') }}" class="stat-card-link">
            <div class="card h-100">
                <div class="card-body">
                    <div class="fw-semibold mb-1"><i class="bi bi-person-plus text-primary me-2"></i>Create AF/AD Profile</div>
                    <div class="text-muted small">Input a new AF/AD account and profile from the admin portal.</div>
                </div>
            </div>
        </a>
    </div>
    <div class="col-md-4">
        <a href="{{ route('admin.appointments.create') }}" class="stat-card-link">
            <div class="card h-100">
                <div class="card-body">
                    <div class="fw-semibold mb-1"><i class="bi bi-calendar-plus text-primary me-2"></i>Create Appointment</div>
                    <div class="text-muted small">Assign AF/AD course appointments without switching role.</div>
                </div>
            </div>
        </a>
    </div>
    <div class="col-md-4">
        <a href="{{ route('admin.users.create', ['role' => 'executive']) }}" class="stat-card-link">
            <div class="card h-100">
                <div class="card-body">
                    <div class="fw-semibold mb-1"><i class="bi bi-person-workspace text-primary me-2"></i>Create School Executive</div>
                    <div class="text-muted small">Add a School Executive login account from the admin portal.</div>
                </div>
            </div>
        </a>
    </div>
    <div class="col-md-4">
        <a href="{{ route('admin.claims.create') }}" class="stat-card-link">
            <div class="card h-100">
                <div class="card-body">
                    <div class="fw-semibold mb-1"><i class="bi bi-file-earmark-plus text-primary me-2"></i>Create Claim</div>
                    <div class="text-muted small">Input a claim record on behalf of AF/AD submissions.</div>
                </div>
            </div>
        </a>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-4">
        <a href="{{ route('admin.classes.create') }}" class="stat-card-link">
            <div class="card h-100">
                <div class="card-body">
                    <div class="fw-semibold mb-1"><i class="bi bi-journal-plus text-primary me-2"></i>Create Class</div>
                    <div class="text-muted small">Input class schedules on behalf of AF/AD users.</div>
                </div>
            </div>
        </a>
    </div>
    <div class="col-md-4">
        <a href="{{ route('admin.certificates.create') }}" class="stat-card-link">
            <div class="card h-100">
                <div class="card-body">
                    <div class="fw-semibold mb-1"><i class="bi bi-award text-primary me-2"></i>Create Certificate</div>
                    <div class="text-muted small">Add and verify certificates for any AF/AD profile.</div>
                </div>
            </div>
        </a>
    </div>
    <div class="col-md-4">
        <a href="{{ route('admin.submissions.create') }}" class="stat-card-link">
            <div class="card h-100">
                <div class="card-body">
                    <div class="fw-semibold mb-1"><i class="bi bi-inbox text-primary me-2"></i>Create Submission</div>
                    <div class="text-muted small">Upload submission records and review states from admin.</div>
                </div>
            </div>
        </a>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div class="text-muted small text-center">
            <i class="bi bi-currency-exchange fs-4 text-success d-block mb-1"></i>
            Total Approved Claim Amount: <strong class="text-success fs-5">RM {{ number_format($stats['total_claim_amount'], 2) }}</strong>
        </div>
    </div>
</div>

</x-layouts.app>
