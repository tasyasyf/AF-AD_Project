<x-layouts.app title="Claim Review">

<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="fw-bold mb-0">Claim: {{ $claim->claim_reference }}</h5>
    <div class="d-flex gap-2">
        @if(in_array($claim->status, ['submitted', 'under_review']))
            <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#approveModal">
                <i class="bi bi-check-circle me-1"></i> Approve
            </button>
            <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#returnModal">
                <i class="bi bi-arrow-counterclockwise me-1"></i> Return
            </button>
            <button class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#rejectModal">
                <i class="bi bi-x-circle me-1"></i> Reject
            </button>
        @endif
        <a href="{{ route('executive.claims.index') }}" class="btn btn-outline-secondary btn-sm">
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
                            <dd class="fw-semibold">{{ $claim->profile->full_name }}</dd>
                            <dt class="text-muted small">Course</dt>
                            <dd>{{ $claim->appointment->course_code }} – {{ $claim->appointment->course_name }}</dd>
                            <dt class="text-muted small">Claim Type</dt>
                            <dd>{{ ucfirst(str_replace('_', ' ', $claim->claim_type)) }}</dd>
                            <dt class="text-muted small">Period</dt>
                            <dd>{{ $claim->period_from->format('d M Y') }} – {{ $claim->period_to->format('d M Y') }}</dd>
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
            </div>
        </div>

        <!-- Supporting Documents -->
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
        <!-- Bank Info -->
        <div class="card mb-4">
            <div class="card-header bg-white fw-semibold">Payment Details</div>
            <div class="card-body small">
                <div><strong>Bank:</strong> {{ $claim->profile->bank_name }}</div>
                <div><strong>Account:</strong> {{ $claim->profile->bank_account_number }}</div>
                <div><strong>Holder:</strong> {{ $claim->profile->bank_account_holder }}</div>
            </div>
        </div>

        <!-- Audit Trail -->
        <div class="card">
            <div class="card-header bg-white fw-semibold">Activity Log</div>
            <div class="card-body p-0">
                @forelse($claim->audits as $audit)
                    <div class="d-flex gap-3 px-3 py-2 border-bottom">
                        <div class="text-muted small text-nowrap">{{ $audit->created_at->format('d M') }}<br>{{ $audit->created_at->format('H:i') }}</div>
                        <div>
                            <div class="fw-semibold small">{{ ucfirst(str_replace('_', ' ', $audit->action)) }}</div>
                            <div class="text-muted small">{{ $audit->performer?->name }}</div>
                            @if($audit->remarks)
                                <div class="text-muted small fst-italic">{{ $audit->remarks }}</div>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="text-center text-muted py-3 small">No activity.</div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<!-- Action Modals -->
<x-confirm-modal
    id="approveModal"
    title="Approve Claim"
    message="Approve claim {{ $claim->claim_reference }} for RM {{ number_format($claim->total_amount, 2) }}?"
    :action="route('executive.claims.approve', $claim)"
    confirmText="Approve"
    confirmClass="btn-success"
    :fields="[['name'=>'remarks','label'=>'Remarks (optional)','required'=>false,'placeholder'=>'Optional remarks...']]"
/>

<x-confirm-modal
    id="returnModal"
    title="Return for Revision"
    message="Return claim {{ $claim->claim_reference }} to the AF/AD for revision."
    :action="route('executive.claims.return', $claim)"
    confirmText="Return"
    confirmClass="btn-warning"
    :fields="[['name'=>'remarks','label'=>'Reason for Return','required'=>true,'placeholder'=>'Explain what needs to be corrected...']]"
/>

<x-confirm-modal
    id="rejectModal"
    title="Reject Claim"
    message="Reject claim {{ $claim->claim_reference }}. This action cannot be undone."
    :action="route('executive.claims.reject', $claim)"
    confirmText="Reject Claim"
    confirmClass="btn-danger"
    :fields="[['name'=>'remarks','label'=>'Reason for Rejection','required'=>true,'placeholder'=>'Enter rejection reason...']]"
/>

</x-layouts.app>
