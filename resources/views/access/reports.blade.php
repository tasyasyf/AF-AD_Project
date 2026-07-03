<x-layouts.app title="Reports (View)">

<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
    <div>
        <h5 class="fw-bold mb-1">Reports</h5>
        <span class="badge bg-secondary"><i class="bi bi-eye"></i> Read-only view · granted by administrator</span>
    </div>
    <a href="{{ route('access.reports.export') }}" class="btn btn-sm btn-outline-primary">
        <i class="bi bi-download"></i> Export CSV
    </a>
</div>

<div class="row g-3 mb-3">
    @foreach([
        ['label' => 'Verified AF/AD', 'value' => $summary['verified_profiles'], 'icon' => 'people-fill'],
        ['label' => 'Total Appointments', 'value' => $summary['total_appointments'], 'icon' => 'calendar3'],
        ['label' => 'Active Appointments', 'value' => $summary['active_appointments'], 'icon' => 'calendar-check'],
        ['label' => 'Submitted / Review', 'value' => $summary['submitted_claims'], 'icon' => 'hourglass-split'],
        ['label' => 'Approved Claims', 'value' => $summary['approved_claims'], 'icon' => 'check2-circle'],
        ['label' => 'PC Endorsed', 'value' => $summary['pc_endorsed_claims'], 'icon' => 'patch-check'],
        ['label' => 'Returned / Rejected', 'value' => $summary['declined_claims'], 'icon' => 'x-circle'],
        ['label' => 'Approved Amount (RM)', 'value' => number_format($summary['approved_claim_amount'], 2), 'icon' => 'cash-coin'],
    ] as $item)
        <div class="col-sm-6 col-xl-3">
            <div class="card stat-card h-100">
                <div class="card-body d-flex align-items-center gap-2">
                    <div class="rounded-circle d-flex align-items-center justify-content-center">
                        <i class="bi bi-{{ $item['icon'] }} fs-4"></i>
                    </div>
                    <div>
                        <div class="text-muted small">{{ $item['label'] }}</div>
                        <div class="fw-bold fs-5">{{ $item['value'] }}</div>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
</div>

<div class="row g-3">
    <div class="col-lg-5">
        <div class="card h-100">
            <div class="card-header bg-white fw-semibold">Claim Status</div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-sm mb-0">
                        <thead class="table-light">
                            <tr><th>Status</th><th class="text-end">Claims</th><th class="text-end">Amount</th></tr>
                        </thead>
                        <tbody>
                            @forelse($claimStatuses as $row)
                                <tr>
                                    <td><x-status-badge :status="$row->status" /></td>
                                    <td class="text-end">{{ $row->total }}</td>
                                    <td class="text-end">RM {{ number_format($row->amount, 2) }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="3" class="text-center text-muted py-3">No data.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-7">
        <div class="card h-100">
            <div class="card-header bg-white fw-semibold">Recent Claims</div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr><th>Reference</th><th>AF/AD</th><th class="text-end">Amount</th><th>Status</th></tr>
                        </thead>
                        <tbody>
                            @forelse($recentClaims as $claim)
                                <tr>
                                    <td class="fw-semibold small">{{ $claim->claim_reference }}</td>
                                    <td class="small">{{ $claim->profile?->full_name }}</td>
                                    <td class="text-end">RM {{ number_format((float) $claim->total_amount, 2) }}</td>
                                    <td><x-status-badge :status="$claim->status" /></td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="text-center text-muted py-4">No claims found.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

</x-layouts.app>
