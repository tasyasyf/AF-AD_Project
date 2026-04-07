<x-layouts.app title="Admin Dashboard">

<h5 class="fw-bold mb-4">System Overview</h5>

<div class="row g-3 mb-4">
    @foreach([
        ['label'=>'Total AF/AD Users', 'value'=>$stats['total_afad'], 'icon'=>'people', 'color'=>'primary'],
        ['label'=>'Registered Profiles', 'value'=>$stats['total_profiles'], 'icon'=>'person-badge', 'color'=>'info'],
        ['label'=>'Verified Profiles', 'value'=>$stats['verified_profiles'], 'icon'=>'check-circle-fill', 'color'=>'success'],
        ['label'=>'Pending Verification', 'value'=>$stats['pending_profiles'], 'icon'=>'hourglass-split', 'color'=>'warning'],
        ['label'=>'Total Appointments', 'value'=>$stats['total_appointments'], 'icon'=>'calendar3', 'color'=>'primary'],
        ['label'=>'Total Claims', 'value'=>$stats['total_claims'], 'icon'=>'file-earmark-text', 'color'=>'secondary'],
        ['label'=>'Approved Claims', 'value'=>$stats['claims_approved'], 'icon'=>'check2-all', 'color'=>'success'],
        ['label'=>'Claims Pending Review', 'value'=>$stats['claims_pending'], 'icon'=>'clock-history', 'color'=>'warning'],
    ] as $s)
    <div class="col-sm-6 col-xl-3">
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
    </div>
    @endforeach
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
