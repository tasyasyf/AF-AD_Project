<x-layouts.app title="Edit Claim">

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h5 class="fw-bold mb-1">Edit Claim</h5>
        <div class="text-muted small">{{ $claim->claim_reference }} · Update the draft details before submitting.</div>
    </div>
    <a href="{{ route('afad.claims.show', $claim) }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i> Back
    </a>
</div>

<form method="POST" action="{{ route('afad.claims.update', $claim) }}" id="claim-form">
@csrf
@method('PUT')

@php
    $formData = $claim->claim_form_data ?? [];
    $partnerName = old('partner_name', $formData['partner_name'] ?? '');
    $school = old('school', $formData['school'] ?? '');
    $learningCentre = old('learning_centre', $formData['learning_centre'] ?? '');
    $selectedProgramme = old('programme', $formData['programme'] ?? '');
    $semesterText = old('semester_text', $formData['semester_text'] ?? $claim->appointment->semester);
    $selectedIntakes = old('semester_intake', $formData['semester_intake'] ?? []);
@endphp

<input type="hidden" name="partner_name" value="{{ $partnerName }}">
<input type="hidden" name="school" value="{{ $school }}">
<input type="hidden" name="learning_centre" value="{{ $learningCentre }}">
<input type="hidden" name="programme" value="{{ $selectedProgramme }}">
<input type="hidden" name="semester_text" value="{{ $semesterText }}">
@foreach($selectedIntakes as $intake)
    <input type="hidden" name="semester_intake[]" value="{{ $intake }}">
@endforeach

<div class="row justify-content-center">
    <div class="col-lg-9">
        <div class="card mb-4">
            <div class="card-header bg-white fw-semibold">Academic Facilitator Information</div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Name</label>
                        <input type="text" class="form-control" value="{{ $claim->profile->full_name }}" disabled>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">NRIC</label>
                        <input type="text" class="form-control" value="{{ $claim->profile->ic_number }}" disabled>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Partner Name</label>
                        <input type="text" id="partner_name" class="form-control" value="{{ $partnerName ?: '-' }}" readonly>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">School</label>
                        <input type="text" id="school" class="form-control" value="{{ $school ?: '-' }}" readonly>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Learning Centre</label>
                        <input type="text" id="learning_centre" class="form-control" value="{{ $learningCentre ?: '-' }}" readonly>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Programme</label>
                        <input type="text" id="programme" class="form-control" value="{{ $selectedProgramme ?: '-' }}" readonly>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Semester</label>
                        <input type="text" id="semester_text" class="form-control" value="{{ $semesterText ?: '-' }}" readonly>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Semester Intake</label>
                        <div class="d-flex flex-wrap gap-2">
                            @foreach(['jan' => 'January', 'may' => 'May', 'sept' => 'September'] as $value => $label)
                                <div class="form-check border rounded px-3 py-2">
                                    <input class="form-check-input ms-0 me-2 semester-intake" type="checkbox" value="{{ $value }}" id="intake_{{ $value }}" {{ in_array($value, $selectedIntakes, true) ? 'checked' : '' }} disabled>
                                    <label class="form-check-label ps-2" for="intake_{{ $value }}">{{ $label }}</label>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header bg-white fw-semibold">Claim Details</div>
            <div class="card-body">
                <div class="mb-4">
                    <label class="form-label fw-semibold">Appointment</label>
                    <input type="text" class="form-control" disabled
                        value="{{ $claim->appointment->course_code }} - {{ $claim->appointment->course_name }} ({{ strtoupper($claim->appointment->role_type) }}, {{ $claim->appointment->semester }})">
                </div>

                @php($claimItems = old('claim_items', $claim->displayClaimItems()))
                <div class="mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <label class="form-label fw-semibold mb-0">Claim Types <span class="text-danger">*</span></label>
                        <button type="button" class="btn btn-sm btn-outline-primary" id="add-claim-type">
                            <i class="bi bi-plus-lg me-1"></i> Add Claim Type
                        </button>
                    </div>
                    <div id="claim-items" class="vstack gap-3">
                        @foreach($claimItems as $index => $item)
                            <div class="border rounded p-3 claim-item">
                                <div class="row g-3 align-items-end">
                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold">Claim Type</label>
                                        <select name="claim_items[{{ $index }}][claim_type]" class="form-select claim-type" required>
                                            @foreach(['teaching'=>'Teaching','marking'=>'Marking','module_development'=>'Module Development','consultation'=>'Consultation'] as $val => $label)
                                                <option value="{{ $val }}" {{ ($item['claim_type'] ?? '') === $val ? 'selected' : '' }}>{{ $label }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-3 claim-hours-wrap">
                                        <label class="form-label fw-semibold">Total Hours</label>
                                        <input type="number" name="claim_items[{{ $index }}][total_hours]" class="form-control claim-hours"
                                            value="{{ $item['total_hours'] ?? '' }}" min="0.5" step="0.01">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label fw-semibold rate-label">Rate per Hour (RM)</label>
                                        <input type="number" name="claim_items[{{ $index }}][rate]" class="form-control claim-rate"
                                            value="{{ $item['rate'] ?? '' }}" min="0" step="0.01" required>
                                    </div>
                                    <div class="col-md-2 d-flex justify-content-end">
                                        <button type="button" class="btn btn-outline-danger remove-claim-type">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="small text-muted mt-2 claim-line-total">Line total: RM 0.00</div>
                            </div>
                        @endforeach
                    </div>
                    @error('claim_items') <div class="text-danger small mt-2">{{ $message }}</div> @enderror
                </div>

                @if(($submissionTotals['amount'] ?? 0) > 0)
                    <div class="alert alert-light border d-flex align-items-start gap-2 mb-4">
                        <i class="bi bi-magic text-primary fs-5"></i>
                        <div>
                            <div class="fw-semibold">Linked uploaded submissions</div>
                            <div class="text-muted small">Submission total: RM {{ number_format($submissionTotals['amount'], 2) }}</div>
                        </div>
                    </div>
                @endif

                <div class="mb-4">
                    <label class="form-label fw-semibold">Submission Checklist</label>
                    <div class="row g-2">
                        <div class="col-md-6">
                            <div class="form-check border rounded px-3 py-2 h-100 {{ $hasRecordingSubmission ? 'bg-light' : '' }}">
                                <input type="checkbox" class="form-check-input ms-0 me-2" id="has_recording_link"
                                    {{ $hasRecordingSubmission ? 'checked' : '' }} disabled>
                                <label class="form-check-label ps-2" for="has_recording_link">
                                    Video Recording Submission
                                    <span class="text-muted small">({{ $hasRecordingSubmission ? 'submitted' : 'not submitted yet' }})</span>
                                </label>
                            </div>
                        </div>
                        @foreach([
                            'has_mark_entry_forms' => 'Mark-entry Forms',
                            'has_graded_scripts' => 'Graded Scripts',
                            'has_qa' => 'Question Paper & Answer Sheet',
                            'has_question_bank_answer_sheet' => 'QB-AS',
                        ] as $field => $label)
                            @php($isSubmitted = $claim->{$field} || ($submissionChecklist[$field] ?? false))
                            <div class="col-md-6">
                                <div class="form-check border rounded px-3 py-2 h-100 {{ $isSubmitted ? 'bg-light' : '' }}">
                                    <input type="checkbox" value="1"
                                        class="form-check-input ms-0 me-2" id="{{ $field }}"
                                        {{ $isSubmitted ? 'checked' : '' }} disabled>
                                    <label class="form-check-label ps-2" for="{{ $field }}">
                                        {{ $label }}
                                        <span class="text-muted small">({{ $isSubmitted ? 'submitted' : 'not submitted yet' }})</span>
                                    </label>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="alert alert-light border d-flex align-items-center gap-2 mb-0">
                    <i class="bi bi-calculator text-primary fs-5"></i>
                    <div>
                        <span class="text-muted small">Estimated Total Amount:</span>
                        <strong class="ms-1" id="total-display">RM {{ number_format($claim->total_amount, 2) }}</strong>
                    </div>
                </div>
            </div>
        </div>

        @include('afad.claims.partials.uploaded-submissions', ['uploadedSubmissions' => $uploadedSubmissions])

        <div class="card mb-4">
            <div class="card-header bg-white fw-semibold">Supporting Documents</div>
            <div class="card-body p-0">
                @if($claim->documents->isEmpty())
                    <div class="text-center text-muted py-4">No supporting document checklist available.</div>
                @else
                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Document</th>
                                    <th>Required</th>
                                    <th>Uploaded File</th>
                                    <th>Uploaded At</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($claim->documents as $document)
                                    <tr>
                                        <td class="fw-semibold">{{ $document->label }}</td>
                                        <td>{{ $document->is_required ? 'Required' : 'Optional' }}</td>
                                        <td>{{ $document->file_original_name ?? '-' }}</td>
                                        <td>{{ $document->uploaded_at?->format('d M Y H:i') ?? '-' }}</td>
                                        <td>
                                            @if($document->is_uploaded)
                                                <span class="badge bg-success">Uploaded</span>
                                            @else
                                                <span class="badge bg-secondary">Not uploaded</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header bg-white fw-semibold">Bank Details</div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Account Holder Name</label>
                        <input type="text" class="form-control" value="{{ $claim->profile->bank_account_holder }}" disabled>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Bank Account Number</label>
                        <input type="text" class="form-control" value="{{ $claim->profile->bank_account_number }}" disabled>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Bank Name</label>
                        <input type="text" class="form-control" value="{{ $claim->profile->bank_name }}" disabled>
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-end gap-2 mb-4">
            <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#printPreviewModal">
                <i class="bi bi-eye me-1"></i> Preview Print
            </button>
            <a href="{{ route('afad.claims.show', $claim) }}" class="btn btn-outline-secondary">Cancel</a>
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-save me-1"></i> Save Changes
            </button>
        </div>
    </div>
</div>
</form>

@include('afad.claims.partials.print-preview-saved', ['claim' => $claim, 'videoRecordingRows' => $videoRecordingRows, 'uploadedSubmissions' => $uploadedSubmissions])

<script>
const submissionBaseAmount = {{ (float) ($submissionTotals['amount'] ?? 0) }};
function money(value) {
    return 'RM ' + value.toFixed(2);
}
function currentClaimItems() {
    return Array.from(document.querySelectorAll('.claim-item')).map((item) => {
        const type = item.querySelector('.claim-type').value;
        const hours = parseFloat(item.querySelector('.claim-hours')?.value) || 0;
        const rate = parseFloat(item.querySelector('.claim-rate').value) || 0;
        return { item, type, total: type === 'teaching' ? hours * rate : rate };
    });
}
function updateTotal() {
    let claimTotal = 0;
    currentClaimItems().forEach(({ item, type, total }) => {
        claimTotal += total;
        const hoursWrap = item.querySelector('.claim-hours-wrap');
        const hoursInput = item.querySelector('.claim-hours');
        const rateLabel = item.querySelector('.rate-label');
        hoursWrap.classList.toggle('d-none', type !== 'teaching');
        hoursInput.toggleAttribute('required', type === 'teaching');
        if (type !== 'teaching') {
            hoursInput.value = '';
        }
        rateLabel.textContent = type === 'teaching' ? 'Rate per Hour (RM)' : 'Rate (RM)';
        item.querySelector('.claim-line-total').textContent = 'Line total: ' + money(total);
    });
    document.getElementById('total-display').textContent = money(claimTotal + submissionBaseAmount);
}
function reindexClaimItems() {
    document.querySelectorAll('.claim-item').forEach((item, index) => {
        item.querySelector('.claim-type').name = `claim_items[${index}][claim_type]`;
        item.querySelector('.claim-hours').name = `claim_items[${index}][total_hours]`;
        item.querySelector('.claim-rate').name = `claim_items[${index}][rate]`;
    });
}
document.getElementById('add-claim-type').addEventListener('click', function () {
    const container = document.getElementById('claim-items');
    const template = container.querySelector('.claim-item').cloneNode(true);
    template.querySelectorAll('input').forEach((input) => input.value = '');
    template.querySelector('.claim-type').value = 'marking';
    container.appendChild(template);
    reindexClaimItems();
    updateTotal();
});
document.getElementById('claim-items').addEventListener('input', updateTotal);
document.getElementById('claim-items').addEventListener('change', updateTotal);
document.getElementById('claim-items').addEventListener('click', function (event) {
    const button = event.target.closest('.remove-claim-type');
    if (button && document.querySelectorAll('.claim-item').length > 1) {
        button.closest('.claim-item').remove();
        reindexClaimItems();
        updateTotal();
    }
});
updateTotal();
</script>

</x-layouts.app>
