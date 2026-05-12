<div class="card mb-4">
    <div class="card-header bg-white fw-semibold">Uploaded Submissions</div>
    @if($showSubmissionFilters ?? false)
        <div class="card-body border-bottom">
            <form method="GET" class="row g-2 align-items-end">
                <div class="col-md-3">
                    <label class="form-label small fw-semibold">Search</label>
                    <input type="text" name="submission_search" class="form-control form-control-sm"
                        value="{{ $submissionFilters['submission_search'] ?? '' }}"
                        placeholder="Title, course, type...">
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-semibold">Type</label>
                    <select name="submission_type" class="form-select form-select-sm">
                        <option value="">All Types</option>
                        @foreach(($submissionTypes ?? []) as $value => $label)
                            <option value="{{ $value }}" {{ ($submissionFilters['submission_type'] ?? '') === $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-semibold">Course</label>
                    <select name="submission_course" class="form-select form-select-sm">
                        <option value="">All Courses</option>
                        @foreach(($submissionCourses ?? []) as $course)
                            <option value="{{ $course }}" {{ ($submissionFilters['submission_course'] ?? '') === $course ? 'selected' : '' }}>{{ $course }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-semibold">From Date</label>
                    <input type="date" name="submission_date_from" class="form-control form-control-sm"
                        value="{{ $submissionFilters['submission_date_from'] ?? '' }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-semibold">To Date</label>
                    <input type="date" name="submission_date_to" class="form-control form-control-sm"
                        value="{{ $submissionFilters['submission_date_to'] ?? '' }}">
                </div>
                <div class="col-md-1 d-flex gap-1">
                    <button type="submit" class="btn btn-sm btn-primary w-100">
                        <i class="bi bi-search"></i>
                    </button>
                    <a href="{{ route('afad.claims.index') }}" class="btn btn-sm btn-outline-secondary">
                        <i class="bi bi-arrow-counterclockwise"></i>
                    </a>
                </div>
            </form>
        </div>
    @endif
    <div class="card-body p-0">
        @if(($uploadedSubmissions ?? collect())->isEmpty())
            <div class="text-center text-muted py-4">
                <i class="bi bi-inbox fs-3 d-block mb-2"></i>
                No submissions uploaded yet.
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Title</th>
                            <th>Type</th>
                            <th>Course</th>
                            <th class="text-end">Hours</th>
                            <th class="text-end">Rate</th>
                            <th class="text-end">Amount</th>
                            <th>Status</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($uploadedSubmissions as $submission)
                            <tr>
                                <td class="fw-semibold">{{ $submission->title }}</td>
                                <td class="small">{{ $submission->type_label }}</td>
                                <td class="small text-muted">
                                    {{ $submission->course ?? '—' }}
                                    @if($submission->course_name)
                                        <br>{{ $submission->course_name }}
                                    @endif
                                    @if($submission->isVideoRecording())
                                        <br>Tutorial {{ $submission->tutorial_number ?? '—' }}
                                    @endif
                                </td>
                                <td class="text-end">{{ $submission->claim_hours ? number_format((float) $submission->claim_hours, 2) : '—' }}</td>
                                <td class="text-end">{{ $submission->rate_per_hour ? 'RM ' . number_format((float) $submission->rate_per_hour, 2) : '—' }}</td>
                                <td class="text-end fw-semibold">{{ $submission->total_amount ? 'RM ' . number_format((float) $submission->total_amount, 2) : '—' }}</td>
                                <td>
                                    @if($submission->status === 'reviewed')
                                        <span class="badge bg-success">Reviewed</span>
                                    @else
                                        <span class="badge bg-warning text-dark">Pending</span>
                                    @endif
                                    @if($submission->isQuestionBankAnswerSheet())
                                        <div class="mt-1">
                                            <span class="badge {{ $submission->pc_qbas_status_badge_class }}">
                                                {{ $submission->pc_qbas_status_label }}
                                            </span>
                                        </div>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('afad.submissions.show', $submission) }}" class="btn btn-sm btn-outline-secondary">View</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
