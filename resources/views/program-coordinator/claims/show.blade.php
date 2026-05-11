<x-layouts.app title="PC Claim Review">

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h5 class="fw-bold mb-1">Claim: {{ $claim->claim_reference }}</h5>
        <div class="text-muted small">Review approved claim, endorse it, then send the printed form to Finance manually.</div>
    </div>
    <div class="d-flex gap-2 flex-wrap">
        @if($claim->status === 'approved' && !$claim->pc_endorsed_at)
            <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#endorseModal">
                <i class="bi bi-check2-circle me-1"></i> Endorse
            </button>
            <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#returnModal">
                <i class="bi bi-arrow-counterclockwise me-1"></i> Return
            </button>
        @endif
        @if($claim->pc_endorsed_at)
            <button type="button" class="btn btn-outline-primary btn-sm" onclick="window.print()">
                <i class="bi bi-printer me-1"></i> Print for Finance
            </button>
        @endif
        <a href="{{ route('pc.claims.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left"></i> Back
        </a>
    </div>
</div>

@if($claim->pc_endorsed_at)
    <div class="alert alert-success d-flex gap-2 mb-4">
        <i class="bi bi-check2-circle fs-5 mt-1"></i>
        <div>
            <strong>Endorsed for manual Finance submission</strong><br>
            <span class="small">
                Endorsed by {{ $claim->pcEndorser?->name }} on {{ $claim->pc_endorsed_at->format('d M Y H:i') }}.
                Print this claim form and submit it to Finance outside the system.
            </span>
        </div>
    </div>
@elseif($claim->status === 'approved')
    <div class="alert alert-info d-flex gap-2 mb-4">
        <i class="bi bi-info-circle fs-5 mt-1"></i>
        <div>
            <strong>Pending Program Coordinator endorsement</strong><br>
            <span class="small">Finance submission is manual. The system only records the endorsement and claim history.</span>
        </div>
    </div>
@endif

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
                            <dd>{{ $claim->appointment->course_code }} - {{ $claim->appointment->course_name }}</dd>
                            <dt class="text-muted small">Semester</dt>
                            <dd>{{ $claim->appointment->semester }} / {{ $claim->appointment->academic_session }}</dd>
                            <dt class="text-muted small">Claim Types</dt>
                            <dd>
                                @foreach($claim->displayClaimItems() as $item)
                                    <div>{{ ucfirst(str_replace('_', ' ', $item['claim_type'])) }} - RM {{ number_format((float) ($item['amount'] ?? 0), 2) }}</div>
                                @endforeach
                            </dd>
                        </dl>
                    </div>
                    <div class="col-md-6">
                        <dl class="mb-0">
                            <dt class="text-muted small">Total Hours</dt>
                            <dd>{{ $claim->total_hours }} hours</dd>
                            <dt class="text-muted small">Summary Rate</dt>
                            <dd>RM {{ number_format($claim->rate_per_hour, 2) }}</dd>
                            <dt class="text-muted small">Total Amount</dt>
                            <dd class="fw-bold text-primary fs-5">RM {{ number_format($claim->total_amount, 2) }}</dd>
                            <dt class="text-muted small">Executive Approved</dt>
                            <dd>{{ $claim->reviewer?->name ?? '-' }} {{ $claim->reviewed_at ? 'on '.$claim->reviewed_at->format('d M Y H:i') : '' }}</dd>
                        </dl>
                    </div>
                </div>

                @if($claim->executive_remarks || $claim->pc_remarks)
                    <hr>
                    @if($claim->executive_remarks)
                        <dt class="text-muted small">Executive Remarks</dt>
                        <dd>{{ $claim->executive_remarks }}</dd>
                    @endif
                    @if($claim->pc_remarks)
                        <dt class="text-muted small">PC Remarks</dt>
                        <dd>{{ $claim->pc_remarks }}</dd>
                    @endif
                @endif

                <hr>
                <div class="text-muted small fw-semibold mb-2">QB-AS / Claim Checklist</div>
                <div class="row g-2">
                    @foreach([
                        'has_mark_entry_forms' => 'Mark-entry Forms',
                        'has_graded_scripts' => 'Graded Scripts',
                        'has_qa' => 'Question Paper',
                        'has_question_bank_answer_sheet' => 'Answer Sheet',
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
        <div class="card mb-4">
            <div class="card-header bg-white fw-semibold">Finance Handling</div>
            <div class="card-body small">
                <div class="mb-2"><strong>Method:</strong> Manual outside system</div>
                <div class="mb-2"><strong>Next step:</strong> Print endorsed claim form and submit to Finance.</div>
                <div class="text-muted">No automatic email, upload, or Finance dispatch is performed by this system.</div>
            </div>
        </div>

        <div class="card">
            <div class="card-header bg-white fw-semibold">Claim History</div>
            <div class="card-body p-0">
                @forelse($claim->audits as $audit)
                    <div class="d-flex gap-3 px-3 py-2 border-bottom">
                        <div class="text-muted small text-nowrap">{{ $audit->created_at->format('d M') }}<br>{{ $audit->created_at->format('H:i') }}</div>
                        <div>
                            <div class="fw-semibold small">
                                @if($audit->metadata['pc_endorsement'] ?? false)
                                    Program Coordinator Endorsed
                                @else
                                    {{ ucfirst(str_replace('_', ' ', $audit->action)) }}
                                @endif
                            </div>
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

<x-confirm-modal
    id="endorseModal"
    title="Endorse Claim"
    message="Endorse claim {{ $claim->claim_reference }} for manual Finance submission?"
    :action="route('pc.claims.endorse', $claim)"
    confirmText="Endorse"
    confirmClass="btn-success"
    :fields="[['name'=>'remarks','label'=>'Remarks (optional)','required'=>false,'placeholder'=>'Optional endorsement note...']]"
/>

<x-confirm-modal
    id="returnModal"
    title="Return Claim"
    message="Return claim {{ $claim->claim_reference }} to the AF/AD for correction."
    :action="route('pc.claims.return', $claim)"
    confirmText="Return"
    confirmClass="btn-warning"
    :fields="[['name'=>'remarks','label'=>'Reason for Return','required'=>true,'placeholder'=>'Explain what needs correction...']]"
/>

</x-layouts.app>
