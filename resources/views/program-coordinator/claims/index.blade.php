<x-layouts.app title="PC Claim Endorsement">

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h5 class="fw-bold mb-1">Claim Endorsement</h5>
        <div class="text-muted small">Monitor all submitted AF/AD claim forms and endorse Executive-approved claims.</div>
    </div>
</div>

<div class="card mb-3">
    <div class="card-body py-2">
        <form method="GET" class="d-flex gap-2 flex-wrap">
            <input type="text" name="search" class="form-control form-control-sm" style="max-width:220px"
                placeholder="Reference / AF/AD..." value="{{ request('search') }}">
            <select name="status" class="form-select form-select-sm" style="max-width:180px">
                <option value="">All Statuses</option>
                @foreach($visibleStatuses as $status)
                    <option value="{{ $status }}" {{ request('status') === $status ? 'selected' : '' }}>{{ ucfirst(str_replace('_', ' ', $status)) }}</option>
                @endforeach
            </select>
            <button class="btn btn-sm btn-primary" type="submit"><i class="bi bi-search"></i> Filter</button>
            <a href="{{ route('pc.claims.index') }}" class="btn btn-sm btn-outline-secondary">Reset</a>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Reference</th>
                        <th>AF/AD</th>
                        <th>Course</th>
                        <th>Type</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>PC Endorsement</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($claims as $claim)
                        <tr>
                            <td class="fw-semibold">{{ $claim->claim_reference }}</td>
                            <td class="small">{{ $claim->profile->full_name }}</td>
                            <td class="small">{{ $claim->appointment->course_code }}</td>
                            <td class="small">{{ ucfirst(str_replace('_', ' ', $claim->claim_type)) }}</td>
                            <td>RM {{ number_format($claim->total_amount, 2) }}</td>
                            <td><x-status-badge :status="$claim->status" /></td>
                            <td>
                                @if($claim->pc_endorsed_at)
                                    <span class="badge bg-success"><i class="bi bi-check2-circle me-1"></i>Endorsed</span>
                                    <div class="text-muted small">{{ $claim->pc_endorsed_at->format('d M Y') }}</div>
                                @elseif($claim->status === 'approved')
                                    <span class="badge bg-warning text-dark"><i class="bi bi-hourglass-split me-1"></i>Pending</span>
                                @else
                                    <span class="text-muted small">Not applicable</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <a href="{{ route('pc.claims.show', $claim) }}" class="btn btn-sm btn-outline-primary">Review</a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="text-center text-muted py-4">No claims found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-3">{{ $claims->links() }}</div>
    </div>
</div>

</x-layouts.app>
