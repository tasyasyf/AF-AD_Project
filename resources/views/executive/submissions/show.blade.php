<x-layouts.app title="Submission Detail">

<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="fw-bold mb-0">{{ $submission->title }}</h5>
    <a href="{{ route('executive.submissions.index') }}" class="btn btn-outline-secondary btn-sm">
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
                    <dt class="col-sm-3 text-muted">AF/AD</dt>
                    <dd class="col-sm-9 fw-semibold">{{ $submission->profile->full_name }}</dd>
                    <dt class="col-sm-3 text-muted">Email</dt>
                    <dd class="col-sm-9">{{ $submission->profile->contact_email }}</dd>
                    <dt class="col-sm-3 text-muted">Title</dt>
                    <dd class="col-sm-9">{{ $submission->title }}</dd>
                    <dt class="col-sm-3 text-muted">Type</dt>
                    <dd class="col-sm-9">{{ $submission->type_label }}</dd>
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
                        @if($submission->hasVideoLink())
                            <dt class="col-sm-3 text-muted">Total Duration</dt>
                            <dd class="col-sm-9">{{ $submission->video_duration_minutes ? number_format($submission->video_duration_minutes, 2) . ' minutes' : '—' }}</dd>
                            <dt class="col-sm-3 text-muted">Video Link</dt>
                            <dd class="col-sm-9">
                                <a href="{{ $submission->video_link }}" target="_blank" rel="noopener" class="text-decoration-none">
                                    {{ $submission->video_link }}
                                </a>
                            </dd>
                        @else
                            <dt class="col-sm-3 text-muted">Total Duration</dt>
                            <dd class="col-sm-9">{{ $submission->video_duration_minutes ? number_format($submission->video_duration_minutes, 2) . ' minutes' : '—' }}</dd>
                        @endif
                    @endif
                    @if($submission->isQuestionBankAnswerSheet())
                        <dt class="col-sm-3 text-muted">Semester Intake</dt>
                        <dd class="col-sm-9">{{ $submission->semester_intake ?? '—' }}</dd>
                        <dt class="col-sm-3 text-muted">Course</dt>
                        <dd class="col-sm-9">{{ $submission->course ?? '—' }}</dd>
                        <dt class="col-sm-3 text-muted">Programme</dt>
                        <dd class="col-sm-9">{{ $submission->programme ?? '—' }}</dd>
                    @endif
                    @if($submission->isMarkEntryForms())
                        <dt class="col-sm-3 text-muted">Course Code</dt>
                        <dd class="col-sm-9">{{ $submission->course ?? '—' }}</dd>
                        <dt class="col-sm-3 text-muted">Course Name</dt>
                        <dd class="col-sm-9">{{ $submission->course_name ?? '—' }}</dd>
                        <dt class="col-sm-3 text-muted">Programme</dt>
                        <dd class="col-sm-9">{{ $submission->programme ?? '—' }}</dd>
                    @endif
                    <dt class="col-sm-3 text-muted">Description</dt>
                    <dd class="col-sm-9">{{ $submission->description ?? '—' }}</dd>
                    <dt class="col-sm-3 text-muted">Uploaded At</dt>
                    <dd class="col-sm-9">{{ $submission->created_at->format('d M Y H:i') }}</dd>
                </dl>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header bg-white fw-semibold">{{ $submission->hasVideoLink() ? 'Video Recording Link' : 'Submitted File' }}</div>
            <div class="card-body">
                <p class="text-muted small mb-3">
                    <i class="bi bi-info-circle me-1"></i>
                    {{ $submission->hasVideoLink() ? 'Open the link below to review the submitted recording.' : 'Click on the file below to download and view the submitted file.' }}
                </p>
                <a href="{{ $submission->hasVideoLink() ? $submission->video_link : route('executive.submissions.download', $submission) }}" target="{{ $submission->hasVideoLink() ? '_blank' : '_self' }}" rel="{{ $submission->hasVideoLink() ? 'noopener' : '' }}" class="text-decoration-none">
                    <div class="border rounded p-3 d-flex align-items-center gap-3 bg-light hover-shadow" style="cursor: pointer;">
                        <i class="bi bi-{{ $submission->hasVideoLink() ? 'link-45deg text-primary' : (str_starts_with($submission->file_mime, 'video/') ? 'camera-video text-danger' : (str_contains($submission->file_mime, 'pdf') ? 'file-earmark-pdf text-danger' : 'file-earmark-text text-primary')) }} fs-1"></i>
                        <div class="flex-grow-1">
                            <div class="fw-semibold text-dark">{{ $submission->hasVideoLink() ? 'Video recording link' : $submission->file_original_name }}</div>
                            <div class="text-muted small text-break">
                                {{ $submission->hasVideoLink() ? $submission->video_link : number_format($submission->file_size / 1024, 1) . ' KB - ' . $submission->file_mime }}
                            </div>
                        </div>
                        <div class="text-primary">
                            <i class="bi bi-{{ $submission->hasVideoLink() ? 'box-arrow-up-right' : 'download' }} fs-4"></i>
                        </div>
                    </div>
                </a>
            </div>
        </div>

        @if($submission->status !== 'reviewed')
            <div class="card">
                <div class="card-header bg-white fw-semibold">Mark as Reviewed</div>
                <div class="card-body">
                    <form method="POST" action="{{ route('executive.submissions.review', $submission) }}">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Remarks (optional)</label>
                            <textarea name="executive_remarks" class="form-control" rows="3" placeholder="Add any feedback for the AF/AD..."></textarea>
                        </div>
                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-check-circle me-1"></i> Mark as Reviewed
                        </button>
                    </form>
                </div>
            </div>
        @endif
    </div>

    <div class="col-lg-4">
        @if($submission->status === 'reviewed')
            <div class="card">
                <div class="card-header bg-white fw-semibold">Review Information</div>
                <div class="card-body small">
                    <div><strong>Reviewed by:</strong> {{ $submission->reviewer?->name }}</div>
                    <div><strong>Reviewed at:</strong> {{ $submission->reviewed_at?->format('d M Y H:i') }}</div>
                    @if($submission->executive_remarks)
                        <hr>
                        <div class="text-muted">Remarks:</div>
                        <div>{{ $submission->executive_remarks }}</div>
                    @endif
                </div>
            </div>
        @endif
    </div>
</div>

</x-layouts.app>
