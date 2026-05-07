<x-layouts.app title="Submission Detail">

<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="fw-bold mb-0">{{ $submission->title }}</h5>
    <a href="{{ route('afad.submissions.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i> Back
    </a>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card mb-4">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <span class="fw-semibold">Submission Details</span>
                @if($submission->status === 'reviewed')
                    <span class="badge bg-success">Reviewed</span>
                @else
                    <span class="badge bg-warning text-dark">Pending Review</span>
                @endif
            </div>
            <div class="card-body">
                <dl class="row mb-0">
                    <dt class="col-sm-3 text-muted">Title</dt>
                    <dd class="col-sm-9">{{ $submission->title }}</dd>
                    <dt class="col-sm-3 text-muted">Type</dt>
                    <dd class="col-sm-9">{{ $submission->type_label }}</dd>
                    <dt class="col-sm-3 text-muted">Course Code</dt>
                    <dd class="col-sm-9">{{ $submission->course ?? '—' }}</dd>
                    <dt class="col-sm-3 text-muted">Course Name</dt>
                    <dd class="col-sm-9">{{ $submission->course_name ?? '—' }}</dd>
                    <dt class="col-sm-3 text-muted">Programme</dt>
                    <dd class="col-sm-9">{{ $submission->programme ?? '—' }}</dd>
                    <dt class="col-sm-3 text-muted">Submission Date</dt>
                    <dd class="col-sm-9">{{ ($submission->submission_date ?? $submission->created_at)->format('d M Y') }}</dd>
                    @if($submission->isVideoRecording())
                        <dt class="col-sm-3 text-muted">Claim Hours</dt>
                        <dd class="col-sm-9">{{ $submission->claim_hours ? number_format($submission->claim_hours, 2) : '—' }}</dd>
                        <dt class="col-sm-3 text-muted">Rate per Hour</dt>
                        <dd class="col-sm-9">{{ $submission->rate_per_hour ? 'RM ' . number_format($submission->rate_per_hour, 2) : '—' }}</dd>
                        <dt class="col-sm-3 text-muted">Total Amount</dt>
                        <dd class="col-sm-9">{{ $submission->total_amount ? 'RM ' . number_format($submission->total_amount, 2) : '—' }}</dd>
                        <dt class="col-sm-3 text-muted">Tutorial</dt>
                        <dd class="col-sm-9">Tutorial {{ $submission->tutorial_number ?? '—' }}</dd>
                        <dt class="col-sm-3 text-muted">Duration</dt>
                        <dd class="col-sm-9">{{ $submission->video_duration_minutes ? number_format($submission->video_duration_minutes, 2) . ' minutes' : '—' }}</dd>
                    @endif
                    @if($submission->isQuestionBankAnswerSheet())
                        <dt class="col-sm-3 text-muted">Semester Intake</dt>
                        <dd class="col-sm-9">{{ $submission->semester_intake ?? '—' }}</dd>
                    @endif
                    <dt class="col-sm-3 text-muted">Description</dt>
                    <dd class="col-sm-9">{{ $submission->description ?? '—' }}</dd>
                    <dt class="col-sm-3 text-muted">Uploaded At</dt>
                    <dd class="col-sm-9">{{ $submission->created_at->format('d M Y H:i') }}</dd>
                </dl>
            </div>
        </div>

        <div class="card">
            <div class="card-header bg-white fw-semibold">Uploaded File</div>
            <div class="card-body">
                <div class="d-flex align-items-center gap-3">
                    <i class="bi bi-{{ str_starts_with($submission->file_mime, 'video/') ? 'camera-video text-danger' : (str_contains($submission->file_mime, 'pdf') ? 'file-earmark-pdf text-danger' : 'file-earmark-text text-primary') }} fs-2"></i>
                    <div class="flex-grow-1">
                        <div class="fw-semibold">{{ $submission->file_original_name }}</div>
                        <div class="text-muted small">{{ number_format($submission->file_size / 1024, 1) }} KB</div>
                    </div>
                    <a href="{{ route('afad.submissions.download', $submission) }}" class="btn btn-outline-primary">
                        <i class="bi bi-download me-1"></i> Download
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        @if($submission->status === 'reviewed')
            <div class="card">
                <div class="card-header bg-white fw-semibold">Executive Review</div>
                <div class="card-body">
                    <div class="small mb-2">
                        <strong>Reviewed by:</strong> {{ $submission->reviewer?->name }}<br>
                        <strong>On:</strong> {{ $submission->reviewed_at?->format('d M Y H:i') }}
                    </div>
                    @if($submission->executive_remarks)
                        <hr>
                        <div class="text-muted small">Remarks:</div>
                        <div class="small">{{ $submission->executive_remarks }}</div>
                    @endif
                </div>
            </div>
        @else
            <div class="card">
                <div class="card-header bg-white fw-semibold">Status</div>
                <div class="card-body text-center">
                    <i class="bi bi-clock-history fs-1 text-warning"></i>
                    <p class="text-muted small mt-2 mb-0">Awaiting review by the School Executive.</p>
                </div>
            </div>
        @endif
    </div>
</div>

</x-layouts.app>
