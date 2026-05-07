<x-layouts.app title="New Claim">

<style>
    .print-preview-sheet {
        color: #202020;
        background: #fff;
        font-size: 0.88rem;
    }
    .print-preview-title {
        font-weight: 800;
        letter-spacing: 0.04em;
        text-align: center;
    }
    .print-logo {
        width: 118px;
        height: auto;
        object-fit: contain;
    }
    .print-line-field {
        display: grid;
        grid-template-columns: 132px 1fr;
        align-items: end;
        gap: 0.6rem;
        min-height: 32px;
    }
    .print-line {
        border-bottom: 1px solid #222;
        min-height: 24px;
        padding: 0 0.25rem;
    }
    .print-boxes {
        display: inline-grid;
        grid-auto-flow: column;
        grid-auto-columns: 20px;
    }
    .print-boxes span,
    .print-semester-box {
        width: 20px;
        height: 20px;
        border: 1px solid #222;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 0.72rem;
    }
    .print-claim-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 0.76rem;
    }
    .print-claim-table th,
    .print-claim-table td {
        border: 1px solid #222;
        padding: 0.32rem;
        vertical-align: middle;
    }
    .print-claim-table th {
        text-align: center;
        font-weight: 700;
    }
    .print-notes {
        font-size: 0.72rem;
        line-height: 1.35;
    }
    .signature-table th,
    .signature-table td {
        height: 68px;
        text-align: center;
    }
    .signature-table .signature-label td {
        height: auto;
        font-size: 0.75rem;
    }

    @media print {
        body,
        #main-content {
            background: #fff !important;
        }
        #sidebar,
        #topbar,
        .page-content > *:not(.modal),
        .modal-header,
        .modal-footer,
        .modal-backdrop {
            display: none !important;
        }
        #main-content {
            margin-left: 0 !important;
        }
        .page-content {
            padding: 0 !important;
        }
        .modal {
            position: static !important;
            display: block !important;
            overflow: visible !important;
        }
        .modal-dialog {
            max-width: none !important;
            margin: 0 !important;
        }
        .modal-content {
            border: 0 !important;
            box-shadow: none !important;
        }
        .modal-body {
            padding: 0 !important;
        }
        .print-preview-sheet {
            padding: 8mm;
            font-size: 9pt;
        }
        .print-claim-table {
            font-size: 7.6pt;
        }
        .print-notes {
            font-size: 7.2pt;
        }
        @page {
            size: A4 portrait;
            margin: 8mm;
        }
    }
</style>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h5 class="fw-bold mb-1">Submit New Claim</h5>
        <div class="text-muted small">Fill in the web form, then preview the printable claim form.</div>
    </div>
    <a href="{{ route('afad.claims.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i> Back
    </a>
</div>

<form method="POST" action="{{ route('afad.claims.store') }}" id="claim-form">
@csrf

<div class="row justify-content-center">
    <div class="col-lg-9">
        <div class="card mb-4">
            <div class="card-header bg-white fw-semibold">Academic Facilitator Information</div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Name</label>
                        <input type="text" class="form-control" value="{{ $profile->full_name }}" disabled>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">NRIC</label>
                        <input type="text" class="form-control" value="{{ $profile->ic_number }}" disabled>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Partner Name</label>
                        <input type="text" id="partner_name" class="form-control" placeholder="Partner / centre name">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">School</label>
                        <input type="text" id="school" class="form-control" placeholder="School">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Learning Centre</label>
                        <input type="text" id="learning_centre" class="form-control" placeholder="Learning centre">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Programme</label>
                        <select id="programme" class="form-select">
                            <option value="">Select programme...</option>
                            @foreach(['BBA', 'BICT', 'BDCM'] as $programme)
                                <option value="{{ $programme }}">{{ $programme }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Semester</label>
                        <input type="text" id="semester_text" class="form-control" placeholder="e.g. May 2026">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Semester Intake</label>
                        <div class="d-flex flex-wrap gap-2">
                            @foreach(['jan' => 'January', 'may' => 'May', 'sept' => 'September'] as $value => $label)
                                <div class="form-check border rounded px-3 py-2">
                                    <input class="form-check-input ms-0 me-2 semester-intake" type="checkbox" value="{{ $value }}" id="intake_{{ $value }}">
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
                <div class="mb-3">
                    <label class="form-label fw-semibold">Appointment <span class="text-danger">*</span></label>
                    <select name="appointment_id" id="appointment_id" class="form-select @error('appointment_id') is-invalid @enderror" required>
                        <option value="">Select appointment...</option>
                        @foreach($appointments as $appt)
                            <option value="{{ $appt->id }}"
                                data-code="{{ $appt->course_code }}"
                                data-name="{{ $appt->course_name }}"
                                data-semester="{{ $appt->semester }}"
                                {{ old('appointment_id') == $appt->id ? 'selected' : '' }}>
                                {{ $appt->course_code }} - {{ $appt->course_name }} ({{ strtoupper($appt->role_type) }}, {{ $appt->semester }})
                            </option>
                        @endforeach
                    </select>
                    @error('appointment_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    @if($appointments->isEmpty())
                        <div class="form-text text-danger">No active appointments found.</div>
                    @endif
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Claim Type <span class="text-danger">*</span></label>
                    <select name="claim_type" id="claim_type" class="form-select @error('claim_type') is-invalid @enderror" required>
                        <option value="">Select type...</option>
                        @foreach(['teaching'=>'Teaching','marking'=>'Marking','module_development'=>'Module Development','consultation'=>'Consultation'] as $val => $label)
                            <option value="{{ $val }}" {{ old('claim_type') === $val ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('claim_type') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Total Hours <span class="text-danger">*</span></label>
                        <input type="number" name="total_hours" id="total_hours"
                            class="form-control @error('total_hours') is-invalid @enderror"
                            value="{{ old('total_hours', $submissionTotals['hours'] ?: '') }}" min="0.5" step="0.01" required>
                        @error('total_hours') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Rate per Hour (RM) <span class="text-danger">*</span></label>
                        <input type="number" name="rate_per_hour" id="rate_per_hour"
                            class="form-control @error('rate_per_hour') is-invalid @enderror"
                            value="{{ old('rate_per_hour', $submissionTotals['rate'] ?: '') }}" min="0" step="0.01" required>
                        @error('rate_per_hour') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                @if(($submissionTotals['amount'] ?? 0) > 0)
                    <div class="alert alert-light border d-flex align-items-start gap-2 mb-4">
                        <i class="bi bi-magic text-primary fs-5"></i>
                        <div>
                            <div class="fw-semibold">Auto-filled from uploaded submissions</div>
                            <div class="text-muted small">
                                {{ number_format($submissionTotals['hours'], 2) }} hours · average RM {{ number_format($submissionTotals['rate'], 2) }}/hour · total RM {{ number_format($submissionTotals['amount'], 2) }}
                            </div>
                        </div>
                    </div>
                @endif

                <div class="mb-4">
                    <label class="form-label fw-semibold">Submission Checklist</label>
                    <div class="row g-2">
                        <div class="col-md-6">
                            <div class="form-check border rounded px-3 py-2 h-100 bg-light">
                                <input type="checkbox" class="form-check-input ms-0 me-2" id="has_recording_link"
                                    {{ $hasRecordingSubmission ? 'checked' : '' }} disabled>
                                <label class="form-check-label ps-2" for="has_recording_link">
                                    Recording Link
                                    <span class="text-muted small">({{ $hasRecordingSubmission ? 'submitted' : 'not submitted yet' }})</span>
                                </label>
                            </div>
                        </div>
                        @foreach([
                            'has_mark_entry_forms' => 'Mark-entry Forms',
                            'has_graded_scripts' => 'Graded Scripts',
                            'has_qa' => 'Question Paper & Answer Sheet',
                        ] as $field => $label)
                            <div class="col-md-6">
                                @php($isSubmitted = $submissionChecklist[$field] ?? false)
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
                        <strong class="ms-1" id="total-display">RM 0.00</strong>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header bg-white fw-semibold">Bank Details</div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Bank Account Number</label>
                        <input type="text" class="form-control" value="{{ $profile->bank_account_number }}" disabled>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Bank Name</label>
                        <input type="text" class="form-control" value="{{ $profile->bank_name }}" disabled>
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-end gap-2 mb-4">
            <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#printPreviewModal" id="preview-print-btn">
                <i class="bi bi-eye me-1"></i> Preview Print Form
            </button>
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-save me-1"></i> Save as Draft
            </button>
        </div>
    </div>
</div>
</form>

<div class="modal fade" id="printPreviewModal" tabindex="-1" aria-labelledby="printPreviewLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="printPreviewLabel">Claim Form Print Preview</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="print-preview-sheet p-4">
                    <div class="text-center small text-muted mb-3">AEU - TMD - FR - 031 - v2.0 Academic Facilitator - Claim Form</div>
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div></div>
                        <h3 class="print-preview-title mb-0">ACADEMIC FACILITATOR - CLAIM FORM</h3>
                        <img src="{{ asset('images/aeu-logo.svg') }}" alt="AEU" class="print-logo">
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <div class="print-line-field"><span>Name</span><span class="print-line">{{ $profile->full_name }}</span></div>
                            <div class="print-line-field"><span>Partner Name</span><span class="print-line" data-preview="partner_name"></span></div>
                            <div class="print-line-field"><span>Learning Centre</span><span class="print-line" data-preview="learning_centre"></span></div>
                        </div>
                        <div class="col-md-6">
                            <div class="print-line-field">
                                <span>NRIC</span>
                                <span>
                                    <span class="print-boxes">
                                        @foreach(str_split(str_pad(preg_replace('/\D/', '', $profile->ic_number), 12)) as $digit)
                                            <span>{{ trim($digit) }}</span>
                                        @endforeach
                                    </span>
                                </span>
                            </div>
                            <div class="print-line-field"><span>School</span><span class="print-line" data-preview="school"></span></div>
                            <div class="print-line-field"><span>Programme</span><span class="print-line" data-preview="programme"></span></div>
                            <div class="print-line-field">
                                <span>Semester</span>
                                <span class="print-line" data-preview="semester_text"></span>
                            </div>
                            <div class="print-line-field">
                                <span>Intake</span>
                                <span class="d-flex align-items-center gap-3">
                                    <span><span class="print-semester-box" id="preview-sem-jan"></span> January</span>
                                    <span><span class="print-semester-box" id="preview-sem-may"></span> May</span>
                                    <span><span class="print-semester-box" id="preview-sem-sept"></span> September</span>
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="print-notes mb-2">
                        <strong><em>Note(s)</em></strong>
                        <ol class="mb-2 ps-3">
                            <li>This form is to be completed and returned to AeU - Talent Management & Development Department (TMD), by end of semester and no later than 7th days after the semester ended.</li>
                            <li>Payment will be made directly to the AF's account as stated in this form within 45 days after the end of semester.</li>
                            <li>Student Attendance Sheet and Mark Entry Form are to be attached together before submitting this form.</li>
                            <li>Failure to provide supporting documents and complete all fields correctly may result in the form being returned.</li>
                        </ol>
                    </div>

                    <table class="print-claim-table mb-3">
                        <thead>
                            <tr>
                                <th rowspan="2" style="width:36px">No</th>
                                <th rowspan="2" style="width:95px">Subject<br>Code</th>
                                <th rowspan="2">Subject Name</th>
                                <th rowspan="2" style="width:58px">Time</th>
                                <th colspan="4">No of Tutorial</th>
                                <th rowspan="2" style="width:64px">Total<br>Hour</th>
                                <th rowspan="2" style="width:72px">Rate /<br>Hour</th>
                                <th rowspan="2" style="width:86px">Total<br>(RM)</th>
                            </tr>
                            <tr>
                                <th>Tutorial 1</th>
                                <th>Tutorial 2</th>
                                <th>Tutorial 3</th>
                                <th>Tutorial 4</th>
                            </tr>
                        </thead>
                        <tbody>
                            @for($i = 1; $i <= 4; $i++)
                                <tr>
                                    <td rowspan="2" class="text-center">{{ $i }}</td>
                                    <td rowspan="2">{{ $i === 1 ? '' : '' }}<span @if($i === 1) id="preview-course-code" @endif></span></td>
                                    <td rowspan="2"><span @if($i === 1) id="preview-course-name" @endif></span></td>
                                    <td class="text-center">Date</td>
                                    <td></td><td></td><td></td><td></td>
                                    <td rowspan="2" class="text-center">{{ $i === 1 ? '' : '0' }}<span @if($i === 1) id="preview-total-hours" @endif></span></td>
                                    <td rowspan="2" class="text-center"><span @if($i === 1) id="preview-rate" @endif></span></td>
                                    <td rowspan="2" class="text-end">{{ $i === 1 ? '' : '-' }}<span @if($i === 1) id="preview-line-total" @endif></span></td>
                                </tr>
                                <tr>
                                    <td class="text-center">Hour</td>
                                    <td></td><td></td><td></td><td></td>
                                </tr>
                            @endfor
                            <tr>
                                <th colspan="10" class="text-end">Total (RM)</th>
                                <th class="text-end" id="preview-table-total">-</th>
                            </tr>
                        </tbody>
                    </table>

                    <div class="fw-semibold mb-1">Other Additional Payments <em>(if applicable)</em></div>
                    <table class="print-claim-table mb-3">
                        <thead>
                            <tr>
                                <th style="width:42px">No</th>
                                <th>Payment Details <em>(i.e: Marking Assignments etc)</em></th>
                                <th style="width:150px">No of Assignments</th>
                                <th style="width:90px">Rate</th>
                                <th style="width:90px">RM</th>
                            </tr>
                        </thead>
                        <tbody>
                            @for($i = 1; $i <= 3; $i++)
                                <tr>
                                    <td class="text-center">{{ $i }}</td>
                                    <td>{{ $i === 1 ? 'Claim type: ' : '' }}<span @if($i === 1) id="preview-claim-type" @endif></span></td>
                                    <td></td>
                                    <td></td>
                                    <td class="text-end">-</td>
                                </tr>
                            @endfor
                            <tr><th colspan="4" class="text-end">Total (RM)</th><th class="text-end">-</th></tr>
                            <tr><th colspan="4" class="text-end fs-6">Grand Total (RM)</th><th class="text-end fs-6" id="preview-grand-total">-</th></tr>
                        </tbody>
                    </table>

                    <div class="fw-semibold text-decoration-underline mb-2">Bank Details</div>
                    <div class="print-line-field"><span>Bank Account Number</span><span class="print-line">{{ $profile->bank_account_number }}</span></div>
                    <div class="print-line-field mb-3"><span>Bank Name</span><span class="print-line">{{ $profile->bank_name }}</span></div>

                    <div class="fw-semibold text-decoration-underline mb-2">Submission Checklist</div>
                    <div class="row g-2 mb-3">
                        @foreach([
                            'has_recording_link' => 'Recording Link',
                            'has_mark_entry_forms' => 'Mark-entry Forms',
                            'has_graded_scripts' => 'Graded Scripts',
                            'has_qa' => 'Question Paper & Answer Sheet',
                        ] as $field => $label)
                            <div class="col-md-6">
                                <span class="print-semester-box" id="preview-{{ str_replace('_', '-', $field) }}"></span>
                                <span class="small ms-1">{{ $label }}</span>
                            </div>
                        @endforeach
                    </div>

                    <table class="print-claim-table signature-table mb-4">
                        <tbody>
                            <tr>
                                <th>Claimed by</th>
                                <th>Verified by Partner /<br>Programme Coordinator</th>
                                <th>Endorsed by Dean</th>
                                <th>AeU TMD Office Only</th>
                            </tr>
                            <tr>
                                <td></td>
                                <td>Assoc. Prof. Rosnah Amal Wan<br>Abd Razak</td>
                                <td>Prof. Ts. Dr. Aedah Abd<br>Rahman</td>
                                <td class="text-start align-top">
                                    <div><strong>Claim Form No</strong> &nbsp; CFN</div>
                                    <hr>
                                    <div><strong>Received Date:</strong></div>
                                </td>
                            </tr>
                            <tr class="signature-label">
                                <td>Name & Signature</td>
                                <td>Name, Signature and Company Stamp</td>
                                <td>Name & Signature</td>
                                <td></td>
                            </tr>
                        </tbody>
                    </table>

                    <div class="small text-muted">Version 2.0, 22nd July 2020</div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="window.print()">
                    <i class="bi bi-printer me-1"></i> Print
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function money(value) {
    return 'RM ' + value.toFixed(2);
}

function selectedAppointment() {
    const select = document.getElementById('appointment_id');
    return select.options[select.selectedIndex];
}

function selectedClaimTypeLabel() {
    const select = document.getElementById('claim_type');
    return select.value ? select.options[select.selectedIndex].text : '';
}

function currentTotal() {
    const hours = parseFloat(document.getElementById('total_hours').value) || 0;
    const rate = parseFloat(document.getElementById('rate_per_hour').value) || 0;
    return { hours, rate, total: hours * rate };
}

function updateTotal() {
    document.getElementById('total-display').textContent = money(currentTotal().total);
}

function syncPrintPreview() {
    const appointment = selectedAppointment();
    const semester = (appointment?.dataset?.semester || '').toLowerCase();
    const totals = currentTotal();
    const intakeChecked = (value) => Boolean(document.getElementById('intake_' + value)?.checked);
    const checked = (id) => Boolean(document.getElementById(id)?.checked);

    document.querySelectorAll('[data-preview]').forEach((target) => {
        const source = document.getElementById(target.dataset.preview);
        target.textContent = source ? source.value : '';
    });

    document.getElementById('preview-course-code').textContent = appointment?.dataset?.code || '';
    document.getElementById('preview-course-name').textContent = appointment?.dataset?.name || '';
    document.getElementById('preview-total-hours').textContent = totals.hours || '0';
    document.getElementById('preview-rate').textContent = totals.rate ? money(totals.rate).replace('RM ', '') : '';
    document.getElementById('preview-line-total').textContent = totals.total ? money(totals.total) : '-';
    document.getElementById('preview-table-total').textContent = totals.total ? money(totals.total) : '-';
    document.getElementById('preview-grand-total').textContent = totals.total ? money(totals.total) : '-';
    document.getElementById('preview-claim-type').textContent = selectedClaimTypeLabel();

    document.getElementById('preview-sem-jan').textContent = intakeChecked('jan') || semester.includes('jan') ? '✓' : '';
    document.getElementById('preview-sem-may').textContent = intakeChecked('may') || semester.includes('may') ? '✓' : '';
    document.getElementById('preview-sem-sept').textContent = intakeChecked('sept') || semester.includes('sept') || semester.includes('sep') ? '✓' : '';

    [
        'has_recording_link',
        'has_mark_entry_forms',
        'has_graded_scripts',
        'has_qa',
    ].forEach((id) => {
        const target = document.getElementById('preview-' + id.replaceAll('_', '-'));
        if (target) {
            target.textContent = checked(id) ? '✓' : '';
        }
    });
}

document.getElementById('total_hours').addEventListener('input', updateTotal);
document.getElementById('rate_per_hour').addEventListener('input', updateTotal);
document.getElementById('printPreviewModal').addEventListener('show.bs.modal', syncPrintPreview);
updateTotal();
</script>

</x-layouts.app>
