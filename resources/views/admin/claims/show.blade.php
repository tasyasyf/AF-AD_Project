<x-layouts.app title="Claim Detail">

<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="fw-bold mb-0">{{ $claim->claim_reference }}</h5>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.claims.edit', $claim) }}" class="btn btn-primary btn-sm">
            <i class="bi bi-pencil me-1"></i> Edit
        </a>
        <form method="POST" action="{{ route('admin.claims.destroy', $claim) }}" onsubmit="return confirm('Delete this claim? This will also remove all linked documents. This cannot be undone.');">
            @csrf @method('DELETE')
            <button type="submit" class="btn btn-danger btn-sm">
                <i class="bi bi-trash me-1"></i> Delete
            </button>
        </form>
        <a href="{{ route('admin.claims.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left"></i> Back
        </a>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card mb-4">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <span class="fw-semibold">Claim Details</span>
                <x-status-badge :status="$claim->status" />
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <dl class="mb-0">
                            <dt class="text-muted small">AF/AD</dt>
                            <dd><a href="{{ route('admin.profiles.show', $claim->profile) }}">{{ $claim->profile->full_name }}</a></dd>
                            <dt class="text-muted small">Course</dt>
                            <dd>{{ $claim->appointment->course_code }} – {{ $claim->appointment->course_name }}</dd>
                            <dt class="text-muted small">Claim Type</dt>
                            <dd>{{ ucfirst(str_replace('_', ' ', $claim->claim_type)) }}</dd>
                        </dl>
                    </div>
                    <div class="col-md-6">
                        <dl class="mb-0">
                            <dt class="text-muted small">Total Hours</dt>
                            <dd>{{ $claim->total_hours }} hours</dd>
                            <dt class="text-muted small">Rate / Hour</dt>
                            <dd>RM {{ number_format($claim->rate_per_hour, 2) }}</dd>
                            <dt class="text-muted small">Total Amount</dt>
                            <dd class="fw-bold text-primary fs-5">RM {{ number_format($claim->total_amount, 2) }}</dd>
                            <dt class="text-muted small">Submitted</dt>
                            <dd>{{ $claim->submitted_at?->format('d M Y H:i') ?? '—' }}</dd>
                        </dl>
                    </div>
                </div>
                @if($claim->executive_remarks)
                    <hr>
                    <dt class="text-muted small">Executive Remarks</dt>
                    <dd>{{ $claim->executive_remarks }}</dd>
                    <dt class="text-muted small">Reviewed By</dt>
                    <dd>{{ $claim->reviewer?->name }}, {{ $claim->reviewed_at?->format('d M Y H:i') }}</dd>
                @endif
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

        <!-- Documents -->
        <div class="card mb-4">
            <div class="card-header bg-white fw-semibold">Supporting Documents</div>
            <div class="card-body">
                @forelse($claim->documents as $doc)
                    <x-document-row :document="$doc" :claim="$claim" :editable="false" />
                @empty
                    <p class="text-muted small mb-0">No documents.</p>
                @endforelse
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <!-- Full Audit Trail -->
        <div class="card">
            <div class="card-header bg-white fw-semibold">Complete Audit Trail</div>
            <div class="card-body p-0">
                @forelse($claim->audits as $audit)
                    <div class="d-flex gap-3 px-3 py-2 border-bottom">
                        <div class="text-muted small text-nowrap">
                            {{ $audit->created_at->format('d M Y') }}<br>
                            <span class="fw-semibold">{{ $audit->created_at->format('H:i') }}</span>
                        </div>
                        <div class="flex-grow-1">
                            <div class="fw-semibold small">{{ ucfirst(str_replace('_', ' ', $audit->action)) }}</div>
                            <div class="text-muted small">by {{ $audit->performer?->name }}</div>
                            @if($audit->from_status && $audit->to_status && $audit->from_status !== $audit->to_status)
                                <div class="small">
                                    <x-status-badge :status="$audit->from_status" />
                                    <i class="bi bi-arrow-right mx-1"></i>
                                    <x-status-badge :status="$audit->to_status" />
                                </div>
                            @endif
                            @if($audit->remarks)
                                <div class="text-muted small fst-italic mt-1">"{{ $audit->remarks }}"</div>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="text-muted small text-center py-4">No audit records.</div>
                @endforelse
            </div>
        </div>
    </div>
</div>

</x-layouts.app>
