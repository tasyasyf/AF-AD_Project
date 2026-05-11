<x-layouts.app title="New Claim">

@php
    $videoRecordingRows = $videoRecordingRows ?? [];
    $printRows = array_slice(array_pad($videoRecordingRows, 4, null), 0, 4);
    $videoRecordingTotal = collect($videoRecordingRows)->sum('amount');
@endphp

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
                        <input type="text" name="partner_name" id="partner_name" class="form-control" value="{{ old('partner_name') }}" placeholder="Partner / centre name">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">School</label>
                        <input type="text" name="school" id="school" class="form-control" value="{{ old('school') }}" placeholder="School">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Learning Centre</label>
                        <input type="text" name="learning_centre" id="learning_centre" class="form-control" value="{{ old('learning_centre') }}" placeholder="Learning centre">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Programme</label>
                        <select name="programme" id="programme" class="form-select">
                            <option value="">Select programme...</option>
                            @foreach(['BBA', 'BICT', 'BDCM'] as $programme)
                                <option value="{{ $programme }}" {{ old('programme') === $programme ? 'selected' : '' }}>{{ $programme }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Semester</label>
                        <input type="text" name="semester_text" id="semester_text" class="form-control" value="{{ old('semester_text') }}" placeholder="e.g. May 2026">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Semester Intake</label>
                        <div class="d-flex flex-wrap gap-2">
                            @foreach(['jan' => 'January', 'may' => 'May', 'sept' => 'September'] as $value => $label)
                                <div class="form-check border rounded px-3 py-2">
                                    <input class="form-check-input ms-0 me-2 semester-intake" type="checkbox" name="semester_intake[]" value="{{ $value }}" id="intake_{{ $value }}" {{ in_array($value, old('semester_intake', []), true) ? 'checked' : '' }}>
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

                @php($claimItems = old('claim_items', [['claim_type' => 'teaching', 'total_hours' => '', 'rate' => '']]))
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
                                            @foreach(['teaching'=>'Teaching','module_development'=>'Module Development','consultation'=>'Consultation'] as $val => $label)
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
                            <div class="fw-semibold">Auto-filled from uploaded submissions</div>
                            <div class="text-muted small">
                                Submission total: RM {{ number_format($submissionTotals['amount'], 2) }}
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
                        <div class="col-md-6">
                            <div class="form-check border rounded px-3 py-2 h-100 {{ $hasAttendanceSubmission ? 'bg-light' : '' }}">
                                <input type="checkbox" class="form-check-input ms-0 me-2" id="has_attendance_sheet"
                                    {{ $hasAttendanceSubmission ? 'checked' : '' }} disabled>
                                <label class="form-check-label ps-2" for="has_attendance_sheet">
                                    Attendance Sheet
                                    <span class="text-muted small">({{ $hasAttendanceSubmission ? 'submitted' : 'not submitted yet' }})</span>
                                </label>
                            </div>
                        </div>
                        @foreach([
                            'has_mark_entry_forms' => 'Mark-entry Forms',
                            'has_graded_scripts' => 'Graded Scripts',
                            'has_qa' => 'Question Paper & Answer Sheet',
                            'has_question_bank_answer_sheet' => 'QB-AS',
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

        @include('afad.claims.partials.uploaded-submissions', ['uploadedSubmissions' => $uploadedSubmissions])

        <div class="card mb-4">
            <div class="card-header bg-white fw-semibold">Bank Details</div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Account Holder Name</label>
                        <input type="text" class="form-control" value="{{ $profile->bank_account_holder }}" disabled>
                    </div>
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
            <button type="submit" name="action" value="draft" class="btn btn-outline-primary">
                <i class="bi bi-save me-1"></i> Save as Draft
            </button>
            <button type="submit" name="action" value="submit" class="btn btn-primary">
                <i class="bi bi-send-fill me-1"></i> Submit Claim
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
                                @php($row = $printRows[$i - 1])
                                <tr>
                                    <td rowspan="2" class="text-center">{{ $i }}</td>
                                    <td rowspan="2">{{ $row['course'] ?? '' }}</td>
                                    <td rowspan="2">{{ $row['course_name'] ?? '' }}</td>
                                    <td class="text-center">Date</td>
                                    @for($tutorial = 0; $tutorial < 4; $tutorial++)
                                        <td>{{ $row['tutorials'][$tutorial]['date'] ?? '' }}</td>
                                    @endfor
                                    <td rowspan="2" class="text-center">{{ $row ? number_format((float) $row['total_hours'], 2) : '0' }}</td>
                                    <td rowspan="2" class="text-center">{{ $row ? number_format((float) $row['rate'], 2) : '' }}</td>
                                    <td rowspan="2" class="text-end">{{ $row ? 'RM ' . number_format((float) $row['amount'], 2) : '-' }}</td>
                                </tr>
                                <tr>
                                    <td class="text-center">Hour</td>
                                    @for($tutorial = 0; $tutorial < 4; $tutorial++)
                                        <td>{{ $row['tutorials'][$tutorial]['hours'] ?? '' }}</td>
                                    @endfor
                                </tr>
                            @endfor
                            <tr>
                                <th colspan="10" class="text-end">Total (RM)</th>
                                <th class="text-end">RM {{ number_format((float) $videoRecordingTotal, 2) }}</th>
                            </tr>
                        </tbody>
                    </table>

                    <div class="fw-semibold mb-1">Other Additional Payments <em>(if applicable)</em></div>
                    <table class="print-claim-table mb-3">
                        <thead>
                            <tr>
                                <th style="width:42px">No</th>
                                <th>Payment Details</th>
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
                    <div class="print-line-field"><span>Account Holder Name</span><span class="print-line">{{ $profile->bank_account_holder }}</span></div>
                    <div class="print-line-field"><span>Bank Account Number</span><span class="print-line">{{ $profile->bank_account_number }}</span></div>
                    <div class="print-line-field mb-3"><span>Bank Name</span><span class="print-line">{{ $profile->bank_name }}</span></div>

                    <div class="fw-semibold text-decoration-underline mb-2">Submission Checklist</div>
                    <div class="row g-2 mb-3">
                        @foreach([
                            'has_recording_link' => 'Recording Link',
                            'has_attendance_sheet' => 'Attendance Sheet',
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
                    @if(($uploadedSubmissions ?? collect())->isNotEmpty())
                        <div class="fw-semibold mb-1">Uploaded Submission(s)</div>
                        <table class="print-claim-table mb-3">
                            <thead>
                                <tr>
                                    <th style="width:36px">No</th>
                                    <th>Submission</th>
                                    <th style="width:120px">Type</th>
                                    <th style="width:95px">Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($uploadedSubmissions as $submission)
                                    <tr>
                                        <td class="text-center">{{ $loop->iteration }}</td>
                                        <td>{{ $submission->title }}</td>
                                        <td>{{ $submission->type_label }}</td>
                                        <td>{{ $submission->submission_date?->format('d/m/Y') ?? $submission->created_at->format('d/m/Y') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif

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
    return Array.from(document.querySelectorAll('.claim-type'))
        .map((select) => select.value ? select.options[select.selectedIndex].text : '')
        .filter(Boolean)
        .join(', ');
}

const submissionBaseAmount = {{ (float) ($submissionTotals['amount'] ?? 0) }};

function currentClaimItems() {
    return Array.from(document.querySelectorAll('.claim-item')).map((item) => {
        const type = item.querySelector('.claim-type').value;
        const hours = parseFloat(item.querySelector('.claim-hours')?.value) || 0;
        const rate = parseFloat(item.querySelector('.claim-rate').value) || 0;
        const total = type === 'teaching' ? hours * rate : rate;
        return { item, type, hours, rate, total };
    });
}

function currentTotal() {
    const items = currentClaimItems();
    const teaching = items.find((item) => item.type === 'teaching') || { hours: 0, rate: 0, total: 0 };
    const claimTotal = items.reduce((sum, item) => sum + item.total, 0);
    return { hours: teaching.hours, rate: teaching.rate, total: claimTotal + submissionBaseAmount, claimTotal, items };
}

function updateTotal() {
    currentClaimItems().forEach(({ item, type, total }) => {
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
    document.getElementById('total-display').textContent = money(currentTotal().total);
}

function reindexClaimItems() {
    document.querySelectorAll('.claim-item').forEach((item, index) => {
        item.querySelector('.claim-type').name = `claim_items[${index}][claim_type]`;
        item.querySelector('.claim-hours').name = `claim_items[${index}][total_hours]`;
        item.querySelector('.claim-rate').name = `claim_items[${index}][rate]`;
    });
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

    document.getElementById('preview-grand-total').textContent = totals.total ? money(totals.total) : '-';
    document.getElementById('preview-claim-type').textContent = selectedClaimTypeLabel();

    document.getElementById('preview-sem-jan').textContent = intakeChecked('jan') || semester.includes('jan') ? '✓' : '';
    document.getElementById('preview-sem-may').textContent = intakeChecked('may') || semester.includes('may') ? '✓' : '';
    document.getElementById('preview-sem-sept').textContent = intakeChecked('sept') || semester.includes('sept') || semester.includes('sep') ? '✓' : '';

    [
        'has_recording_link',
        'has_attendance_sheet',
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

document.getElementById('add-claim-type').addEventListener('click', function () {
    const container = document.getElementById('claim-items');
    const template = container.querySelector('.claim-item').cloneNode(true);
    template.querySelectorAll('input').forEach((input) => input.value = '');
    template.querySelector('.claim-type').value = 'module_development';
    container.appendChild(template);
    reindexClaimItems();
    updateTotal();
});

document.getElementById('claim-items').addEventListener('input', updateTotal);
document.getElementById('claim-items').addEventListener('change', updateTotal);
document.getElementById('claim-items').addEventListener('click', function (event) {
    const button = event.target.closest('.remove-claim-type');
    if (!button) {
        return;
    }
    if (document.querySelectorAll('.claim-item').length > 1) {
        button.closest('.claim-item').remove();
        reindexClaimItems();
        updateTotal();
    }
});
document.getElementById('printPreviewModal').addEventListener('show.bs.modal', syncPrintPreview);
updateTotal();
</script>

</x-layouts.app>
