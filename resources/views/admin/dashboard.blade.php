<x-layouts.app title="Admin Dashboard">

<style>
    .admin-metric-card .card-body {
        min-height: 78px;
        padding: 0.85rem 1rem;
    }

    .admin-metric-card .rounded-circle {
        width: 40px !important;
        height: 40px !important;
        flex: 0 0 40px;
    }

    .admin-metric-card .rounded-circle i {
        font-size: 1rem !important;
    }

    .admin-metric-card .text-muted.small {
        margin-bottom: 0.15rem;
        font-size: 0.74rem;
    }

    .admin-metric-card .fw-bold.fs-4 {
        font-size: 1.35rem !important;
    }

    .admin-panel-item {
        min-height: 76px;
        border: 1px solid #f2d5d1;
        border-radius: 6px;
        padding: 0.8rem 0.9rem;
    }
</style>

<div class="d-flex justify-content-between align-items-start mb-4 flex-wrap gap-3">
    <div>
        <h5 class="fw-bold mb-1">System Overview</h5>
        <div class="text-muted small">Monitor portal activity and manage core admin records.</div>
    </div>
    <div class="d-flex gap-2 flex-wrap">
        <a href="{{ route('admin.users.create') }}" class="btn btn-primary btn-sm">
            <i class="bi bi-person-plus me-1"></i> New User
        </a>
        <a href="{{ route('admin.appointments.create') }}" class="btn btn-outline-primary btn-sm">
            <i class="bi bi-calendar-plus me-1"></i> New Appointment
        </a>
        <a href="{{ route('admin.claims.create') }}" class="btn btn-outline-primary btn-sm">
            <i class="bi bi-file-earmark-plus me-1"></i> New Claim
        </a>
    </div>
</div>

<div class="row g-3 mb-3">
    @foreach([
        ['label'=>'AF/AD Users', 'value'=>$stats['total_afad'], 'icon'=>'people', 'href'=>route('admin.profiles.index')],
        ['label'=>'School Executives', 'value'=>$stats['total_executives'], 'icon'=>'person-workspace', 'href'=>route('admin.users.index', ['role'=>'executive'])],
        ['label'=>'Program Coordinators', 'value'=>$stats['total_pc'], 'icon'=>'person-check', 'href'=>route('admin.users.index', ['role'=>'pc'])],
        ['label'=>'Admin Users', 'value'=>$stats['total_admins'], 'icon'=>'person-gear', 'href'=>route('admin.users.index', ['role'=>'admin'])],
    ] as $s)
        <div class="col-sm-6 col-xl-3">
            <a href="{{ $s['href'] }}" class="stat-card-link">
                <div class="card stat-card admin-metric-card h-100">
                    <div class="card-body d-flex align-items-center gap-2">
                        <div class="rounded-circle d-flex align-items-center justify-content-center">
                            <i class="bi bi-{{ $s['icon'] }}"></i>
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

<div class="row g-3 mb-3">
    <div class="col-xl-5">
        <div class="card h-100">
            <div class="card-header bg-white fw-semibold">Profiles</div>
            <div class="card-body">
                <div class="row g-2">
                    @foreach([
                        ['label'=>'Registered', 'value'=>$stats['total_profiles'], 'icon'=>'person-badge', 'href'=>route('admin.profiles.index')],
                        ['label'=>'Verified', 'value'=>$stats['verified_profiles'], 'icon'=>'check-circle-fill', 'href'=>route('admin.profiles.index', ['status'=>'verified'])],
                        ['label'=>'Pending', 'value'=>$stats['pending_profiles'], 'icon'=>'hourglass-split', 'href'=>route('admin.profiles.index', ['status'=>'pending'])],
                    ] as $s)
                        <div class="col-md-4">
                            <a href="{{ $s['href'] }}" class="stat-card-link">
                                <div class="admin-panel-item h-100">
                                    <div class="text-primary mb-1"><i class="bi bi-{{ $s['icon'] }}"></i></div>
                                    <div class="text-muted small">{{ $s['label'] }}</div>
                                    <div class="fw-bold fs-5">{{ $s['value'] }}</div>
                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-7">
        <div class="card h-100">
            <div class="card-header bg-white fw-semibold">Operations</div>
            <div class="card-body">
                <div class="row g-2">
                    @foreach([
                        ['label'=>'Appointments', 'value'=>$stats['total_appointments'], 'icon'=>'calendar3', 'href'=>route('admin.appointments.index')],
                        ['label'=>'Total Claims', 'value'=>$stats['total_claims'], 'icon'=>'file-earmark-text', 'href'=>route('admin.claims.index')],
                        ['label'=>'Pending Claims', 'value'=>$stats['claims_pending'], 'icon'=>'clock-history', 'href'=>route('admin.claims.index', ['status'=>'submitted'])],
                        ['label'=>'Approved Claims', 'value'=>$stats['claims_approved'], 'icon'=>'check2-all', 'href'=>route('admin.claims.index', ['status'=>'approved'])],
                        ['label'=>'Pending PC Endorsement', 'value'=>$stats['claims_pending_pc'], 'icon'=>'clipboard-check', 'href'=>route('admin.claims.index', ['status'=>'approved'])],
                        ['label'=>'PC Endorsed', 'value'=>$stats['claims_pc_endorsed'], 'icon'=>'patch-check', 'href'=>route('admin.claims.index', ['status'=>'approved'])],
                    ] as $s)
                        <div class="col-md-6">
                            <a href="{{ $s['href'] }}" class="stat-card-link">
                                <div class="admin-panel-item d-flex align-items-center justify-content-between gap-2">
                                    <div class="d-flex align-items-center gap-2">
                                        <i class="bi bi-{{ $s['icon'] }} text-primary"></i>
                                        <span class="fw-semibold small">{{ $s['label'] }}</span>
                                    </div>
                                    <span class="fw-bold fs-5 text-nowrap">{{ $s['value'] }}</span>
                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body py-3 d-flex align-items-center justify-content-between gap-3 flex-wrap">
        <div class="d-flex align-items-center gap-3">
            <div class="rounded-circle d-flex align-items-center justify-content-center bg-success bg-opacity-10" style="width:42px;height:42px;flex:0 0 42px">
                <i class="bi bi-currency-exchange text-success"></i>
            </div>
            <div>
                <div class="fw-semibold">Approved Claim Amount</div>
                <div class="text-muted small">Total approved for payment</div>
            </div>
        </div>
        <div class="d-flex align-items-center gap-3 flex-wrap">
            <div class="fw-bold fs-4 text-success">RM {{ number_format($stats['total_claim_amount'], 2) }}</div>
            <a href="{{ route('admin.claims.index', ['status'=>'approved']) }}" class="btn btn-sm btn-outline-primary">
                View Approved Claims
            </a>
        </div>
    </div>
</div>

</x-layouts.app>
