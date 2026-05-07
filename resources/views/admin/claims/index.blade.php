<x-layouts.app title="All Claims">

<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="fw-bold mb-0">All Claims</h5>
    <a href="{{ route('admin.claims.create') }}" class="btn btn-primary btn-sm">
        <i class="bi bi-file-earmark-plus me-1"></i> New Claim
    </a>
</div>

<div class="card mb-3">
    <div class="card-body py-2">
        <form method="GET" class="d-flex gap-2 flex-wrap">
            <input type="text" name="search" class="form-control form-control-sm" style="max-width:220px"
                placeholder="Reference / name..." value="{{ request('search') }}">
            <select name="status" class="form-select form-select-sm" style="max-width:180px">
                <option value="">All Statuses</option>
                @foreach(['draft','submitted','under_review','approved','returned','rejected'] as $s)
                    <option value="{{ $s }}" {{ request('status') === $s ? 'selected' : '' }}>{{ ucfirst(str_replace('_',' ',$s)) }}</option>
                @endforeach
            </select>
            <button class="btn btn-sm btn-primary" type="submit"><i class="bi bi-search"></i> Filter</button>
            <a href="{{ route('admin.claims.index') }}" class="btn btn-sm btn-outline-secondary">Reset</a>
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
                        <th>Amount (RM)</th>
                        <th>Submitted</th>
                        <th>Status</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($claims as $claim)
                    <tr>
                        <td class="fw-semibold">{{ $claim->claim_reference }}</td>
                        <td class="small">{{ $claim->profile->full_name }}</td>
                        <td class="small">{{ $claim->appointment->course_code }}</td>
                        <td class="small">{{ ucfirst(str_replace('_',' ',$claim->claim_type)) }}</td>
                        <td>{{ number_format($claim->total_amount, 2) }}</td>
                        <td class="small text-muted">{{ $claim->submitted_at?->format('d M Y') ?? '—' }}</td>
                        <td><x-status-badge :status="$claim->status" /></td>
                        <td>
                            <div class="d-flex gap-1">
                                <a href="{{ route('admin.claims.show', $claim) }}" class="btn btn-sm btn-outline-secondary">View</a>
                                <a href="{{ route('admin.claims.edit', $claim) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                                <form method="POST" action="{{ route('admin.claims.destroy', $claim) }}" onsubmit="return confirm('Delete this claim? This cannot be undone.');" class="d-inline">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                                </form>
                            </div>
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
