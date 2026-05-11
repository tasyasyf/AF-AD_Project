<x-layouts.app title="Program Coordinator Dashboard">

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h5 class="fw-bold mb-1">Program Coordinator Dashboard</h5>
        <div class="text-muted small">Monitor verified AF/AD readiness, nominations, appointments, and claim decisions.</div>
    </div>
</div>

<div class="row g-3 mb-4">
    @foreach([
        ['label' => 'Pending Endorsements', 'value' => $stats['pending_pc_reviews'], 'icon' => 'hourglass-split', 'color' => 'warning', 'href' => route('pc.claims.index', ['status' => 'approved'])],
        ['label' => 'Active AF/AD', 'value' => $stats['active_afad'], 'icon' => 'people-fill', 'color' => 'success', 'href' => route('pc.afad.index')],
        ['label' => 'Active Appointments', 'value' => $stats['active_permissions'], 'icon' => 'calendar-check', 'color' => 'primary', 'href' => route('pc.appointments.index')],
        ['label' => 'Monthly Endorsed', 'value' => $stats['monthly_endorsed'], 'icon' => 'check2-circle', 'color' => 'success', 'href' => route('pc.claims.index', ['status' => 'approved'])],
        ['label' => 'Monthly Declines', 'value' => $stats['monthly_declined'], 'icon' => 'x-circle', 'color' => 'danger', 'href' => route('pc.claims.index', ['status' => 'rejected'])],
    ] as $s)
        <div class="col-sm-6 col-xl">
            <a href="{{ $s['href'] }}" class="stat-card-link">
                <div class="card stat-card h-100">
                    <div class="card-body d-flex align-items-center gap-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center bg-{{ $s['color'] }} bg-opacity-10">
                            <i class="bi bi-{{ $s['icon'] }} fs-4 text-{{ $s['color'] }}"></i>
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
    @foreach([
        ['label' => 'AF/AD List', 'icon' => 'people', 'href' => route('pc.afad.index')],
        ['label' => 'Appointments', 'icon' => 'calendar3', 'href' => route('pc.appointments.index')],
        ['label' => 'Nomination', 'icon' => 'person-check', 'href' => route('pc.nomination.index')],
        ['label' => 'Document Checklist', 'icon' => 'clipboard-check', 'href' => route('pc.document-checklist.index')],
        ['label' => 'Claim Review', 'icon' => 'file-earmark-check', 'href' => route('pc.claims.index')],
        ['label' => 'Reports', 'icon' => 'bar-chart', 'href' => route('pc.reports.index')],
    ] as $shortcut)
        <div class="col-sm-6 col-lg">
            <a href="{{ $shortcut['href'] }}" class="stat-card-link">
                <div class="card h-100">
                    <div class="card-body d-flex align-items-center gap-2">
                        <i class="bi bi-{{ $shortcut['icon'] }} text-primary fs-4"></i>
                        <span class="fw-semibold">{{ $shortcut['label'] }}</span>
                    </div>
                </div>
            </a>
        </div>
    @endforeach
</div>

<div class="row g-4">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <span class="fw-semibold">Pending Endorsements</span>
                <a href="{{ route('pc.claims.index', ['status' => 'approved']) }}" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
            <div class="card-body p-0">
                @forelse($pendingClaims as $claim)
                    <div class="d-flex justify-content-between align-items-center px-3 py-2 border-bottom">
                        <div>
                            <div class="fw-semibold small">{{ $claim->claim_reference }}</div>
                            <div class="text-muted small">{{ $claim->profile->full_name }} &bull; {{ $claim->appointment->course_code }}</div>
                        </div>
                        <x-status-badge :status="$claim->status" />
                    </div>
                @empty
                    <div class="text-center text-muted py-4 small">No pending endorsements.</div>
                @endforelse
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="card">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <span class="fw-semibold">Verified AF/AD Ready for Nomination</span>
                <a href="{{ route('pc.afad.index') }}" class="btn btn-sm btn-outline-primary">Open List</a>
            </div>
            <div class="card-body p-0">
                @forelse($verifiedProfiles as $profile)
                    <div class="d-flex justify-content-between align-items-center px-3 py-2 border-bottom">
                        <div>
                            <div class="fw-semibold small">{{ $profile->full_name }}</div>
                            <div class="text-muted small">
                                {{ $profile->appointments->first()?->course_code ?? 'No active appointment' }}
                            </div>
                        </div>
                        <a href="{{ route('pc.afad.show', $profile) }}" class="btn btn-sm btn-outline-primary">Select</a>
                    </div>
                @empty
                    <div class="text-center text-muted py-4 small">No verified AF/AD profiles yet.</div>
                @endforelse
            </div>
        </div>
    </div>
</div>

</x-layouts.app>
