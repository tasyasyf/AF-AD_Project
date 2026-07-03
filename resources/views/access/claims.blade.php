<x-layouts.app title="Claims (View)">

<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
    <div>
        <h5 class="fw-bold mb-1">Claims</h5>
        <span class="badge bg-secondary"><i class="bi bi-eye"></i> Read-only view · granted by administrator</span>
    </div>
    <form method="GET" class="d-flex gap-2 flex-wrap">
        <select name="status" class="form-select form-select-sm" style="max-width:170px" onchange="this.form.submit()">
            <option value="">All Statuses</option>
            @foreach(['submitted','under_review','approved','returned','rejected'] as $s)
                <option value="{{ $s }}" {{ $status === $s ? 'selected' : '' }}>{{ ucfirst(str_replace('_',' ',$s)) }}</option>
            @endforeach
        </select>
        <input type="text" name="search" value="{{ $search }}" class="form-control form-control-sm" style="max-width:200px" placeholder="Reference / AF/AD...">
        <button class="btn btn-sm btn-primary"><i class="bi bi-search"></i></button>
    </form>
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
                        <th class="text-end">Amount</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($claims as $claim)
                        <tr class="detail-trigger" data-detail="detail-{{ $claim->id }}" data-title="Claim {{ $claim->claim_reference }}">
                            <td class="fw-semibold">{{ $claim->claim_reference }}</td>
                            <td class="small">{{ $claim->profile?->full_name }}</td>
                            <td class="small">{{ $claim->appointment?->course_code }}</td>
                            <td class="text-end">RM {{ number_format((float) $claim->total_amount, 2) }}</td>
                            <td><x-status-badge :status="$claim->status" /></td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center text-muted py-4">No claims found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="mt-3">{{ $claims->links() }}</div>

{{-- Hidden detail blocks (rendered into the modal on row click) --}}
<div class="d-none">
    @foreach($claims as $claim)
        <div id="detail-{{ $claim->id }}">
            <dl class="row mb-3">
                <dt class="col-sm-4 text-muted">AF/AD</dt>
                <dd class="col-sm-8">{{ $claim->profile?->full_name ?? '—' }}</dd>
                <dt class="col-sm-4 text-muted">Course</dt>
                <dd class="col-sm-8">{{ $claim->appointment?->course_code }} — {{ $claim->appointment?->course_name }}</dd>
                <dt class="col-sm-4 text-muted">Period</dt>
                <dd class="col-sm-8">{{ $claim->period_from?->format('d M Y') }} – {{ $claim->period_to?->format('d M Y') }}</dd>
                <dt class="col-sm-4 text-muted">Total Hours</dt>
                <dd class="col-sm-8">{{ number_format((float) $claim->total_hours, 2) }}</dd>
                <dt class="col-sm-4 text-muted">Total Amount</dt>
                <dd class="col-sm-8 fw-bold">RM {{ number_format((float) $claim->total_amount, 2) }}</dd>
                <dt class="col-sm-4 text-muted">Status</dt>
                <dd class="col-sm-8">{{ ucfirst(str_replace('_', ' ', $claim->status)) }}</dd>
                @if($claim->executive_remarks)
                    <dt class="col-sm-4 text-muted">Executive Remarks</dt>
                    <dd class="col-sm-8">{{ $claim->executive_remarks }}</dd>
                @endif
                @if($claim->pc_remarks)
                    <dt class="col-sm-4 text-muted">PC Remarks</dt>
                    <dd class="col-sm-8">{{ $claim->pc_remarks }}</dd>
                @endif
            </dl>

            <div class="fw-semibold small text-muted mb-1">Claim Items</div>
            <div class="table-responsive mb-3">
                <table class="table table-sm mb-0">
                    <thead class="table-light">
                        <tr><th>Type</th><th class="text-end">Hours</th><th class="text-end">Rate</th><th class="text-end">Amount</th></tr>
                    </thead>
                    <tbody>
                        @foreach($claim->displayClaimItems() as $item)
                            <tr>
                                <td>{{ ucfirst(str_replace('_', ' ', $item['claim_type'] ?? '')) }}</td>
                                <td class="text-end">{{ isset($item['total_hours']) && $item['total_hours'] !== null ? number_format((float) $item['total_hours'], 2) : '—' }}</td>
                                <td class="text-end">RM {{ number_format((float) ($item['rate'] ?? 0), 2) }}</td>
                                <td class="text-end">RM {{ number_format((float) ($item['amount'] ?? 0), 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="fw-semibold small text-muted mb-1">History</div>
            <ul class="list-group list-group-flush">
                @forelse($claim->audits as $audit)
                    <li class="list-group-item px-0 py-2">
                        <div class="fw-semibold small">{{ ucfirst(str_replace('_', ' ', $audit->action)) }}</div>
                        <div class="text-muted small">{{ $audit->performer?->name }} · {{ $audit->created_at->format('d M Y H:i') }}</div>
                        @if($audit->remarks)
                            <div class="small mt-1">{{ $audit->remarks }}</div>
                        @endif
                    </li>
                @empty
                    <li class="list-group-item px-0 py-2 text-muted small">No history.</li>
                @endforelse
            </ul>
        </div>
    @endforeach
</div>

@include('access.partials.detail-modal')

</x-layouts.app>
