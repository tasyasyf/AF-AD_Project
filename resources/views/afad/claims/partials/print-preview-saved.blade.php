@php
    $formData = $claim->claim_form_data ?? [];
    $selectedIntakes = $formData['semester_intake'] ?? [];
    $uploadedSubmissions = $uploadedSubmissions ?? collect();
    $videoRecordingRows = $videoRecordingRows ?? [];
    $printRows = array_slice(array_pad($videoRecordingRows, 4, null), 0, 4);
    $videoRecordingTotal = collect($videoRecordingRows)->sum('amount');
    $additionalSubmissionRows = $uploadedSubmissions
        ->reject(fn($submission) => $submission->isVideoRecording())
        ->values();
    $additionalSubmissionTotal = $additionalSubmissionRows->sum(fn($submission) => (float) ($submission->total_amount ?? 0));
@endphp

<style>
    .print-preview-sheet { color: #202020; background: #fff; font-size: 0.88rem; }
    .print-preview-title { font-weight: 800; letter-spacing: 0.04em; text-align: center; }
    .print-logo { width: 118px; height: auto; object-fit: contain; }
    .print-line-field { display: grid; grid-template-columns: 132px 1fr; align-items: end; gap: 0.6rem; min-height: 32px; }
    .print-line { border-bottom: 1px solid #222; min-height: 24px; padding: 0 0.25rem; }
    .print-boxes { display: inline-grid; grid-auto-flow: column; grid-auto-columns: 20px; }
    .print-boxes span, .print-semester-box { width: 20px; height: 20px; border: 1px solid #222; display: inline-flex; align-items: center; justify-content: center; font-size: 0.72rem; }
    .print-claim-table { width: 100%; border-collapse: collapse; font-size: 0.76rem; }
    .print-claim-table th, .print-claim-table td { border: 1px solid #222; padding: 0.32rem; vertical-align: middle; }
    .print-claim-table th { text-align: center; font-weight: 700; }
    .print-notes { font-size: 0.72rem; line-height: 1.35; }
    .signature-table th, .signature-table td { height: 68px; text-align: center; }
    .signature-table .signature-label td { height: auto; font-size: 0.75rem; }
    @media print {
        body, #main-content { background: #fff !important; }
        #sidebar, #topbar, .page-content > *:not(.modal), .modal-header, .modal-footer, .modal-backdrop { display: none !important; }
        #main-content { margin-left: 0 !important; }
        .page-content { padding: 0 !important; }
        .modal { position: static !important; display: block !important; overflow: visible !important; }
        .modal-dialog { max-width: none !important; margin: 0 !important; }
        .modal-content { border: 0 !important; box-shadow: none !important; }
        .modal-body { padding: 0 !important; }
        .print-preview-sheet { padding: 8mm; font-size: 9pt; }
        .print-claim-table { font-size: 7.6pt; }
        .print-notes { font-size: 7.2pt; }
        @page { size: A4 portrait; margin: 8mm; }
    }
</style>

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
                            <div class="print-line-field"><span>Name</span><span class="print-line">{{ $claim->profile->full_name }}</span></div>
                            <div class="print-line-field"><span>Partner Name</span><span class="print-line">{{ $formData['partner_name'] ?? '' }}</span></div>
                            <div class="print-line-field"><span>Learning Centre</span><span class="print-line">{{ $formData['learning_centre'] ?? '' }}</span></div>
                        </div>
                        <div class="col-md-6">
                            <div class="print-line-field">
                                <span>NRIC</span>
                                <span class="print-boxes">
                                    @foreach(str_split(str_pad(preg_replace('/\D/', '', $claim->profile->ic_number), 12)) as $digit)
                                        <span>{{ trim($digit) }}</span>
                                    @endforeach
                                </span>
                            </div>
                            <div class="print-line-field"><span>School</span><span class="print-line">{{ $formData['school'] ?? '' }}</span></div>
                            <div class="print-line-field"><span>Programme</span><span class="print-line">{{ $formData['programme'] ?? ($claim->appointment->programme ?? '') }}</span></div>
                            <div class="print-line-field"><span>Semester</span><span class="print-line">{{ $formData['semester_text'] ?? $claim->appointment->semester }}</span></div>
                            <div class="print-line-field">
                                <span>Intake</span>
                                <span class="d-flex align-items-center gap-3">
                                    <span><span class="print-semester-box">{{ in_array('jan', $selectedIntakes, true) ? '✓' : '' }}</span> January</span>
                                    <span><span class="print-semester-box">{{ in_array('may', $selectedIntakes, true) ? '✓' : '' }}</span> May</span>
                                    <span><span class="print-semester-box">{{ in_array('sept', $selectedIntakes, true) ? '✓' : '' }}</span> September</span>
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
                            <tr><th>Tutorial 1</th><th>Tutorial 2</th><th>Tutorial 3</th><th>Tutorial 4</th></tr>
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
                            <tr><th colspan="10" class="text-end">Total (RM)</th><th class="text-end">RM {{ number_format((float) $videoRecordingTotal, 2) }}</th></tr>
                        </tbody>
                    </table>

                    <div class="fw-semibold mb-1">Other Additional Payments <em>(if applicable)</em></div>
                    <table class="print-claim-table mb-3">
                        <thead><tr><th style="width:42px">No</th><th>Payment Details</th><th style="width:150px">No of Assignments</th><th style="width:90px">Rate</th><th style="width:90px">RM</th></tr></thead>
                        <tbody>
                            @forelse($additionalSubmissionRows as $submission)
                                <tr>
                                    <td class="text-center">{{ $loop->iteration }}</td>
                                    <td>
                                        {{ $submission->type_label }}
                                        @if($submission->course)
                                            - {{ $submission->course }}
                                        @endif
                                        @if($submission->course_name)
                                            / {{ $submission->course_name }}
                                        @endif
                                    </td>
                                    <td></td>
                                    <td>{{ $submission->rate_per_hour ? number_format((float) $submission->rate_per_hour, 2) : '-' }}</td>
                                    <td class="text-end">{{ $submission->total_amount ? 'RM ' . number_format((float) $submission->total_amount, 2) : '-' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td class="text-center">1</td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td class="text-end">-</td>
                                </tr>
                            @endforelse
                            <tr><th colspan="4" class="text-end">Total (RM)</th><th class="text-end">RM {{ number_format((float) $additionalSubmissionTotal, 2) }}</th></tr>
                            <tr><th colspan="4" class="text-end fs-6">Grand Total (RM)</th><th class="text-end fs-6">RM {{ number_format($claim->total_amount, 2) }}</th></tr>
                        </tbody>
                    </table>

                    <div class="fw-semibold text-decoration-underline mb-2">Bank Details</div>
                    <div class="print-line-field"><span>Account Holder Name</span><span class="print-line">{{ $claim->profile->bank_account_holder }}</span></div>
                    <div class="print-line-field"><span>Bank Account Number</span><span class="print-line">{{ $claim->profile->bank_account_number }}</span></div>
                    <div class="print-line-field mb-3"><span>Bank Name</span><span class="print-line">{{ $claim->profile->bank_name }}</span></div>

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
