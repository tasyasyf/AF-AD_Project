<x-layouts.app title="My Claims">

<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="fw-bold mb-0">My Claims</h5>
    <a href="{{ route('afad.claims.create') }}" class="btn btn-primary btn-sm">
        <i class="bi bi-plus-circle me-1"></i> New Claim
    </a>
</div>

<div class="card">
    <div class="card-body p-0">
        @if($claims->isEmpty())
            <div class="text-center text-muted py-5">
                <i class="bi bi-file-earmark-text fs-1 d-block mb-2"></i>
                No claims yet. <a href="{{ route('afad.claims.create') }}">Submit your first claim</a>.
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Reference</th>
                            <th>Course</th>
                            <th>Type</th>
                            <th>Period</th>
                            <th>Hours</th>
                            <th>Amount (RM)</th>
                            <th>Status</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($claims as $claim)
                        <tr>
                            <td class="fw-semibold">{{ $claim->claim_reference }}</td>
                            <td>{{ $claim->appointment->course_code }}</td>
                            <td class="small">{{ ucfirst(str_replace('_',' ',$claim->claim_type)) }}</td>
                            <td class="small text-muted">
                                {{ $claim->period_from->format('d M') }} – {{ $claim->period_to->format('d M Y') }}
                            </td>
                            <td>{{ $claim->total_hours }}</td>
                            <td>{{ number_format($claim->total_amount, 2) }}</td>
                            <td><x-status-badge :status="$claim->status" /></td>
                            <td>
                                <a href="{{ route('afad.claims.show', $claim) }}" class="btn btn-sm btn-outline-secondary">View</a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="p-3">{{ $claims->links() }}</div>
        @endif
    </div>
</div>

</x-layouts.app>
