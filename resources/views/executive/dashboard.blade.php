<x-layouts.app title="Executive Dashboard">

<div class="row g-3 mb-4">
    @foreach([
        ['label'=>'Pending Profiles', 'value'=>$stats['pending_profiles'], 'icon'=>'hourglass-split', 'color'=>'warning', 'href'=>route('executive.profiles.index', ['status'=>'pending'])],
        ['label'=>'Verified Profiles', 'value'=>$stats['verified_profiles'], 'icon'=>'check-circle-fill', 'color'=>'success', 'href'=>route('executive.profiles.index', ['status'=>'verified'])],
        ['label'=>'Claims for Review', 'value'=>$stats['claims_submitted'] + $stats['claims_under_review'], 'icon'=>'file-earmark-check', 'color'=>'primary', 'href'=>route('executive.claims.index', ['status'=>'submitted'])],
        ['label'=>'Approved Claims', 'value'=>$stats['claims_approved'], 'icon'=>'check2-all', 'color'=>'success', 'href'=>route('executive.claims.index', ['status'=>'approved'])],
    ] as $s)
    <div class="col-sm-6 col-xl-3">
        <a href="{{ $s['href'] }}" class="stat-card-link">
            <div class="card stat-card h-100" style="border-color:var(--bs-{{ $s['color'] }})">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center bg-{{ $s['color'] }} bg-opacity-10"
                        style="width:52px;height:52px;flex-shrink:0">
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

<div class="row g-4">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <span class="fw-semibold">Pending Profile Verifications</span>
                <a href="{{ route('executive.profiles.index', ['status'=>'pending']) }}" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
            <div class="card-body p-0">
                @forelse($pending_profiles as $profile)
                    <div class="d-flex justify-content-between align-items-center px-3 py-2 border-bottom">
                        <div>
                            <div class="fw-semibold small">{{ $profile->full_name }}</div>
                            <div class="text-muted small">{{ $profile->user->email }}</div>
                        </div>
                        <a href="{{ route('executive.profiles.show', $profile) }}" class="btn btn-sm btn-outline-primary">Review</a>
                    </div>
                @empty
                    <div class="text-center text-muted py-4 small">No pending profiles.</div>
                @endforelse
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="card">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <span class="fw-semibold">Recent Claims</span>
                <a href="{{ route('executive.claims.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
            <div class="card-body p-0">
                @forelse($pending_claims as $claim)
                    <div class="d-flex justify-content-between align-items-center px-3 py-2 border-bottom">
                        <div>
                            <div class="fw-semibold small">{{ $claim->claim_reference }}</div>
                            <div class="text-muted small">
                                {{ $claim->profile->full_name }} &bull; RM {{ number_format($claim->total_amount, 2) }}
                            </div>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <x-status-badge :status="$claim->status" />
                            <a href="{{ route('executive.claims.show', $claim) }}" class="btn btn-sm btn-outline-primary">Review</a>
                        </div>
                    </div>
                @empty
                    <div class="text-center text-muted py-4 small">No claims yet.</div>
                @endforelse
            </div>
        </div>
    </div>
</div>

</x-layouts.app>
