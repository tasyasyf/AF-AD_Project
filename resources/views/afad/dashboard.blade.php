<x-layouts.app title="Dashboard">

<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <a href="{{ $profile ? route('afad.profile.show') : route('afad.profile.create') }}" class="stat-card-link">
            <div class="card stat-card h-100" style="border-color:var(--portal-red)">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center">
                        <i class="bi bi-person-check"></i>
                    </div>
                    <div>
                        <div class="text-muted small">Profile Status</div>
                        <x-status-badge :status="$stats['profile_status']" />
                    </div>
                </div>
            </div>
        </a>
    </div>
    <div class="col-sm-6 col-xl-3">
        <a href="{{ route('afad.appointments.index') }}" class="stat-card-link">
            <div class="card stat-card h-100" style="border-color:#198754">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center bg-success bg-opacity-10" style="width:52px;height:52px">
                        <i class="bi bi-calendar3 fs-4 text-success"></i>
                    </div>
                    <div>
                        <div class="text-muted small">Appointments</div>
                        <div class="fw-bold fs-4">{{ $stats['total_appointments'] }}</div>
                    </div>
                </div>
            </div>
        </a>
    </div>
    <div class="col-sm-6 col-xl-3">
        <a href="{{ route('afad.claims.index') }}" class="stat-card-link">
            <div class="card stat-card h-100" style="border-color:#ffc107">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center bg-warning bg-opacity-10" style="width:52px;height:52px">
                        <i class="bi bi-hourglass-split fs-4 text-warning"></i>
                    </div>
                    <div>
                        <div class="text-muted small">Pending Claims</div>
                        <div class="fw-bold fs-4">{{ $stats['pending_claims'] }}</div>
                    </div>
                </div>
            </div>
        </a>
    </div>
    <div class="col-sm-6 col-xl-3">
        <a href="{{ route('afad.claims.index') }}" class="stat-card-link">
            <div class="card stat-card h-100" style="border-color:#198754">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center bg-success bg-opacity-10" style="width:52px;height:52px">
                        <i class="bi bi-check-circle fs-4 text-success"></i>
                    </div>
                    <div>
                        <div class="text-muted small">Approved Claims</div>
                        <div class="fw-bold fs-4">{{ $stats['approved_claims'] }}</div>
                    </div>
                </div>
            </div>
        </a>
    </div>
</div>

@if(!$profile)
    <div class="alert alert-warning d-flex align-items-center gap-2">
        <i class="bi bi-exclamation-triangle-fill fs-5"></i>
        <div>
            You have not registered your profile yet.
            <a href="{{ route('afad.profile.create') }}" class="alert-link">Register your profile</a> to get started.
        </div>
    </div>
@elseif($profile->status === 'rejected')
    <div class="alert alert-danger d-flex align-items-center gap-2">
        <i class="bi bi-x-circle-fill fs-5"></i>
        <div>
            Your profile was <strong>rejected</strong>. Reason: {{ $profile->rejection_reason }}<br>
            <a href="{{ route('afad.profile.edit') }}" class="alert-link">Update your profile</a> and resubmit.
        </div>
    </div>
@elseif($profile->status === 'pending')
    <div class="alert alert-info d-flex align-items-center gap-2">
        <i class="bi bi-hourglass-split fs-5"></i>
        <div>Your profile is <strong>pending verification</strong> by the School Executive.</div>
    </div>
@endif

<div class="card">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <span class="fw-semibold">Recent Claims</span>
        <a href="{{ route('afad.claims.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
    </div>
    <div class="card-body p-0">
        @if($recent_claims->isEmpty())
            <div class="text-center text-muted py-5">
                <i class="bi bi-file-earmark-text fs-1 d-block mb-2"></i>
                No claims yet.
                @if($profile && $profile->status === 'verified')
                    <a href="{{ route('afad.claims.create') }}">Submit your first claim</a>
                @endif
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Reference</th>
                            <th>Course</th>
                            <th>Type</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recent_claims as $claim)
                        <tr>
                            <td class="fw-semibold">{{ $claim->claim_reference }}</td>
                            <td>{{ $claim->appointment->course_code }}</td>
                            <td>{{ ucfirst(str_replace('_', ' ', $claim->claim_type)) }}</td>
                            <td>RM {{ number_format($claim->total_amount, 2) }}</td>
                            <td><x-status-badge :status="$claim->status" /></td>
                            <td><a href="{{ route('afad.claims.show', $claim) }}" class="btn btn-xs btn-outline-secondary btn-sm">View</a></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>

</x-layouts.app>
