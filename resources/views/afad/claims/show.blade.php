<x-layouts.app title="Claim Detail">

<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="fw-bold mb-0">{{ $claim->claim_reference }}</h5>
    <div class="d-flex gap-2">
        <button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#printPreviewModal">
            <i class="bi bi-eye me-1"></i> Preview Print
        </button>
        @if(in_array($claim->status, ['draft', 'returned']))
            <a href="{{ route('afad.claims.edit', $claim) }}" class="btn btn-outline-primary btn-sm">
                <i class="bi bi-pencil me-1"></i> Edit
            </a>
        @endif
        @if(in_array($claim->status, ['draft', 'returned']))
            <form method="POST" action="{{ route('afad.claims.submit', $claim) }}">
                @csrf
                <button type="submit" class="btn btn-success btn-sm">
                    <i class="bi bi-send-fill me-1"></i> Submit Claim
                </button>
            </form>
        @endif
        @if($claim->status === 'draft')
            <form method="POST" action="{{ route('afad.claims.destroy', $claim) }}"
                onsubmit="return confirm('Delete this draft claim?')">
                @csrf @method('DELETE')
                <button type="submit" class="btn btn-outline-danger btn-sm">
                    <i class="bi bi-trash me-1"></i> Delete
                </button>
            </form>
        @endif
        <a href="{{ route('afad.claims.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left"></i> Back
        </a>
    </div>
</div>

@if($claim->status === 'returned' && ($claim->executive_remarks || $claim->pc_remarks))
    <div class="alert alert-warning d-flex gap-2 mb-4">
        <i class="bi bi-arrow-counterclockwise fs-5 mt-1"></i>
        <div>
            <strong>Returned for Revision</strong><br>
            <span class="small">{{ $claim->pc_remarks ?? $claim->executive_remarks }}</span>
        </div>
    </div>
@endif

@if($claim->pc_endorsed_at)
    <div class="alert alert-success d-flex gap-2 mb-4">
        <i class="bi bi-check2-circle fs-5 mt-1"></i>
        <div>
            <strong>Endorsed by Program Coordinator</strong><br>
            <span class="small">Your claim has been endorsed. Finance submission is handled manually outside the system.</span>
        </div>
    </div>
@endif

<div class="row g-4">
    <div class="col-lg-7">
        <div class="card mb-4">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <span class="fw-semibold">Claim Details</span>
                <x-status-badge :status="$claim->status" />
            </div>
            <div class="card-body">
                <dl class="row mb-0">
                    <dt class="col-sm-4 text-muted">Reference</dt>
                    <dd class="col-sm-8 fw-semibold">{{ $claim->claim_reference }}</dd>
                    <dt class="col-sm-4 text-muted">Course</dt>
                    <dd class="col-sm-8">{{ $claim->appointment->course_code }} – {{ $claim->appointment->course_name }}</dd>
                    <dt class="col-sm-4 text-muted">Claim Types</dt>
                    <dd class="col-sm-8">
                        <div class="table-responsive">
                            <table class="table table-sm align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th>Type</th>
                                        <th class="text-end">Hours</th>
                                        <th class="text-end">Rate</th>
                                        <th class="text-end">Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($claim->displayClaimItems() as $item)
                                        <tr>
                                            <td>{{ ucfirst(str_replace('_', ' ', $item['claim_type'])) }}</td>
                                            <td class="text-end">{{ ($item['claim_type'] ?? '') === 'teaching' ? number_format((float) ($item['total_hours'] ?? 0), 2) : '—' }}</td>
                                            <td class="text-end">RM {{ number_format((float) ($item['rate'] ?? 0), 2) }}</td>
                                            <td class="text-end">RM {{ number_format((float) ($item['amount'] ?? 0), 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </dd>
                    <dt class="col-sm-4 text-muted">Total Amount</dt>
                    <dd class="col-sm-8 fw-bold text-primary fs-5">RM {{ number_format($claim->total_amount, 2) }}</dd>
                    @if($claim->submitted_at)
                        <dt class="col-sm-4 text-muted">Submitted</dt>
                        <dd class="col-sm-8">{{ $claim->submitted_at->format('d M Y H:i') }}</dd>
                    @endif
                    @if($claim->executive_remarks)
                        <dt class="col-sm-4 text-muted">Executive Remarks</dt>
                        <dd class="col-sm-8">{{ $claim->executive_remarks }}</dd>
                    @endif
                    @if($claim->pc_remarks)
                        <dt class="col-sm-4 text-muted">PC Remarks</dt>
                        <dd class="col-sm-8">{{ $claim->pc_remarks }}</dd>
                    @endif
                    @if($claim->pc_endorsed_at)
                        <dt class="col-sm-4 text-muted">PC Endorsed</dt>
                        <dd class="col-sm-8">{{ $claim->pc_endorsed_at->format('d M Y H:i') }}</dd>
                    @endif
                </dl>
                <hr>
                <div class="text-muted small fw-semibold mb-2">Submission Checklist</div>
                <div class="row g-2">
                    @foreach([
                        'has_mark_entry_forms' => 'Mark-entry Forms',
                        'has_graded_scripts' => 'Graded Scripts',
                        'has_qa' => 'Question Paper & Answer Sheet',
                    ] as $field => $label)
                        <div class="col-md-6">
                            <span class="badge bg-{{ $claim->{$field} ? 'success' : 'secondary' }}">
                                <i class="bi bi-{{ $claim->{$field} ? 'check2' : 'dash' }} me-1"></i>{{ $label }}
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

    </div>

    <div class="col-lg-5">
        <!-- Audit Trail -->
        <div class="card">
            <div class="card-header bg-white fw-semibold">Activity Log</div>
            <div class="card-body p-0">
                @forelse($claim->audits as $audit)
                    <div class="d-flex gap-3 px-3 py-2 border-bottom">
                        <div class="text-muted small text-nowrap">{{ $audit->created_at->format('d M Y') }}<br>{{ $audit->created_at->format('H:i') }}</div>
                        <div>
                            <div class="fw-semibold small">{{ ucfirst(str_replace('_', ' ', $audit->action)) }}</div>
                            <div class="text-muted small">{{ $audit->performer?->name }}</div>
                            @if($audit->remarks)
                                <div class="text-muted small fst-italic">{{ $audit->remarks }}</div>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="text-muted small text-center py-4">No activity yet.</div>
                @endforelse
            </div>
        </div>
    </div>
</div>

@include('afad.claims.partials.print-preview-saved', ['claim' => $claim, 'videoRecordingRows' => $videoRecordingRows])

</x-layouts.app>
