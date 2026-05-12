<x-layouts.app title="Document Checklist">

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h5 class="fw-bold mb-1">Document Checklist</h5>
        <div class="text-muted small">Monitor uploaded Attendance, QB-AS, and MEF files by course appointment.</div>
    </div>
</div>

<div class="card mb-3">
    <div class="card-body py-2">
        <form method="GET" class="d-flex gap-2 flex-wrap">
            <select name="course" class="form-select form-select-sm" style="max-width:180px">
                <option value="">All Courses</option>
                @foreach($courses as $course)
                    <option value="{{ $course }}" {{ ($filters['course'] ?? '') === $course ? 'selected' : '' }}>{{ $course }}</option>
                @endforeach
            </select>
            <select name="semester" class="form-select form-select-sm" style="max-width:180px">
                <option value="">All Semesters</option>
                @foreach($semesters as $semester)
                    <option value="{{ $semester }}" {{ ($filters['semester'] ?? '') === $semester ? 'selected' : '' }}>{{ $semester }}</option>
                @endforeach
            </select>
            <button class="btn btn-sm btn-primary" type="submit"><i class="bi bi-search"></i> Filter</button>
            <a href="{{ route('pc.document-checklist.index') }}" class="btn btn-sm btn-outline-secondary">Reset</a>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Course</th>
                        <th>AF/AD Name</th>
                        <th>Role</th>
                        <th class="text-center">Attendance</th>
                        <th class="text-center">QB-AS</th>
                        <th class="text-center">MEF</th>
                        <th class="text-end">Review</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($appointments as $appointment)
                        @php
                            $roleLabels = [
                                'af' => 'AF',
                                'ad' => 'AD',
                                'af_internal' => 'AF Internal',
                                'ad_internal' => 'AD Internal',
                            ];
                            $roleClass = str_contains($appointment->role_type, 'af') ? 'bg-primary' : 'bg-info';
                            $submissions = $appointment->profile->submissions
                                ->where('course', $appointment->course_code)
                                ->whereNotNull('file_path');

                            $attendanceSubmission = $submissions
                                ->where('submission_type', \App\Models\Submission::TYPE_ATTENDANCE_SHEET)
                                ->sortByDesc('created_at')
                                ->first();
                            $hasLegacyAttendance = $appointment->claims
                                ->flatMap(fn ($claim) => $claim->documents)
                                ->contains(fn ($document) => $document->document_type === 'attendance_sheet' && $document->is_uploaded);
                            $hasAttendance = (bool) $attendanceSubmission || $hasLegacyAttendance;

                            $qbAsSubmission = $submissions
                                ->where('submission_type', \App\Models\Submission::TYPE_QUESTION_BANK_ANSWER_SHEET)
                                ->sortByDesc('created_at')
                                ->first();
                            $hasQbAs = (bool) $qbAsSubmission;
                            $mefSubmission = $submissions
                                ->where('submission_type', \App\Models\Submission::TYPE_MARK_ENTRY_FORMS)
                                ->sortByDesc('created_at')
                                ->first();
                            $hasMef = (bool) $mefSubmission;
                            $allDocumentsComplete = $hasAttendance && $hasQbAs && $hasMef;
                        @endphp
                        <tr>
                            <td>
                                <div class="fw-semibold">{{ $appointment->course_code }}</div>
                                <div class="text-muted small">{{ $appointment->semester }}</div>
                            </td>
                            <td class="fw-semibold">{{ $appointment->profile->full_name }}</td>
                            <td>
                                <span class="badge {{ $roleClass }}">
                                    {{ $roleLabels[$appointment->role_type] ?? strtoupper($appointment->role_type) }}
                                </span>
                            </td>
                            <td class="text-center">
                                <div class="d-inline-flex flex-column align-items-center justify-content-center gap-1" style="min-height:54px;min-width:130px">
                                    @if($hasAttendance)
                                        <i class="bi bi-check-circle-fill text-success fs-5" title="Uploaded"></i>
                                    @else
                                        <i class="bi bi-circle text-muted fs-5" title="Not uploaded"></i>
                                    @endif
                                    <span style="height:22px"></span>
                                </div>
                            </td>
                            <td class="text-center">
                                <div class="d-inline-flex flex-column align-items-center justify-content-center gap-1" style="min-height:54px;min-width:130px">
                                    @if($qbAsSubmission)
                                        <i class="bi bi-check-circle-fill text-success fs-5" title="Uploaded"></i>
                                        <span class="badge {{ $qbAsSubmission->pc_qbas_status_badge_class }}">
                                            {{ $qbAsSubmission->pc_qbas_status_label }}
                                        </span>
                                    @else
                                        <i class="bi bi-circle text-muted fs-5" title="Not uploaded"></i>
                                        <span style="height:22px"></span>
                                    @endif
                                </div>
                            </td>
                            <td class="text-center">
                                <div class="d-inline-flex flex-column align-items-center justify-content-center gap-1" style="min-height:54px;min-width:130px">
                                    @if($hasMef)
                                        <i class="bi bi-check-circle-fill text-success fs-5" title="Uploaded"></i>
                                    @else
                                        <i class="bi bi-circle text-muted fs-5" title="Not uploaded"></i>
                                    @endif
                                    <span style="height:22px"></span>
                                </div>
                            </td>
                            <td class="text-end">
                                <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#documentReviewModal{{ $appointment->id }}">
                                    <i class="bi bi-clipboard-check me-1"></i> Review
                                </button>
                            </td>
                        </tr>
                        <div class="modal fade" id="documentReviewModal{{ $appointment->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Review Documents</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <div class="fw-semibold">{{ $appointment->profile->full_name }}</div>
                                            <div class="text-muted small">{{ $appointment->course_code }} - {{ $appointment->course_name }} · {{ $appointment->semester }}</div>
                                        </div>

                                        <div class="list-group mb-3">
                                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                                <div>
                                                    <div>Attendance Sheet</div>
                                                    @if($attendanceSubmission)
                                                        <a href="{{ route('pc.document-checklist.submissions.view', $attendanceSubmission) }}" target="_blank" rel="noopener" class="small text-decoration-none">
                                                            <i class="bi bi-box-arrow-up-right me-1"></i>View uploaded file
                                                        </a>
                                                    @elseif($hasLegacyAttendance)
                                                        <div class="small text-muted">Uploaded in old claim document records.</div>
                                                    @endif
                                                </div>
                                                <span class="badge {{ $hasAttendance ? 'bg-success' : 'bg-secondary' }}">{{ $hasAttendance ? 'Uploaded' : 'Missing' }}</span>
                                            </div>
                                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                                <div>
                                                    <div>QB-AS</div>
                                                    @if($qbAsSubmission)
                                                        <a href="{{ route('pc.document-checklist.submissions.view', $qbAsSubmission) }}" target="_blank" rel="noopener" class="small text-decoration-none">
                                                            <i class="bi bi-box-arrow-up-right me-1"></i>View uploaded file
                                                        </a>
                                                    @endif
                                                </div>
                                                <span class="badge {{ $hasQbAs ? 'bg-success' : 'bg-secondary' }}">{{ $hasQbAs ? 'Uploaded' : 'Missing' }}</span>
                                            </div>
                                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                                <div>
                                                    <div>MEF</div>
                                                    @if($mefSubmission)
                                                        <a href="{{ route('pc.document-checklist.submissions.view', $mefSubmission) }}" target="_blank" rel="noopener" class="small text-decoration-none">
                                                            <i class="bi bi-box-arrow-up-right me-1"></i>View uploaded file
                                                        </a>
                                                    @endif
                                                </div>
                                                <span class="badge {{ $hasMef ? 'bg-success' : 'bg-secondary' }}">{{ $hasMef ? 'Uploaded' : 'Missing' }}</span>
                                            </div>
                                        </div>

                                        @if($qbAsSubmission)
                                            <div class="border rounded p-3 mb-3">
                                                <div class="d-flex justify-content-between align-items-center gap-2">
                                                    <div>
                                                        <div class="fw-semibold">QB-AS Quantity Check</div>
                                                        <div class="text-muted small">Confirm only when AF/AD uploaded 2 sets in one upload.</div>
                                                    </div>
                                                    <span class="badge {{ $qbAsSubmission->pc_qbas_status_badge_class }}">{{ $qbAsSubmission->pc_qbas_status_label }}</span>
                                                </div>
                                                @if($qbAsSubmission->pc_qbas_set_count)
                                                    <div class="small text-muted mt-2">{{ $qbAsSubmission->pc_qbas_set_count }} set recorded.</div>
                                                @endif
                                                @if($qbAsSubmission->pc_qbas_remarks)
                                                    <div class="small mt-2">{{ $qbAsSubmission->pc_qbas_remarks }}</div>
                                                @endif
                                            </div>
                                        @endif

                                        @if(!$allDocumentsComplete)
                                            <div class="alert alert-warning small mb-0">
                                                Complete Attendance Sheet, QB-AS, and MEF before confirming.
                                            </div>
                                        @elseif($qbAsSubmission && $qbAsSubmission->pc_qbas_status !== 'confirmed')
                                            <form method="POST" action="{{ route('pc.document-checklist.qbas.confirm', $qbAsSubmission) }}" class="mb-3">
                                                @csrf
                                                <input type="hidden" name="pc_qbas_set_count" value="2">
                                                <button type="submit" class="btn btn-success w-100">
                                                    <i class="bi bi-check2-circle me-1"></i> Confirm
                                                </button>
                                            </form>
                                        @elseif($qbAsSubmission && $qbAsSubmission->pc_qbas_status === 'confirmed')
                                            <div class="alert alert-success small mb-0">QB-AS quantity has been confirmed.</div>
                                        @endif

                                        @if($qbAsSubmission && $qbAsSubmission->pc_qbas_status !== 'confirmed')
                                            <hr>
                                            <form method="POST" action="{{ route('pc.document-checklist.qbas.reject', $qbAsSubmission) }}" class="row g-2">
                                                @csrf
                                                <input type="hidden" name="pc_qbas_set_count" value="1">
                                                <div class="col-12">
                                                    <label class="form-label small fw-semibold">Reject Reason</label>
                                                    <input type="text" name="pc_qbas_remarks" class="form-control" placeholder="Example: QB-AS is less than 2 sets" required>
                                                </div>
                                                <div class="col-12">
                                                    <button type="submit" class="btn btn-outline-danger w-100">
                                                        <i class="bi bi-x-circle me-1"></i> Reject
                                                    </button>
                                                </div>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">No appointments found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-3">{{ $appointments->links() }}</div>
    </div>
</div>

</x-layouts.app>
