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
                    <dt class="col-sm-3 text-muted">Description</dt>
                    <dd class="col-sm-9">{{ $submission->description ?? '—' }}</dd>
                    <dt class="col-sm-3 text-muted">Submitted</dt>
                    <dd class="col-sm-9">{{ $submission->created_at->format('d M Y H:i') }}</dd>
                </dl>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header bg-white fw-semibold">Submitted File</div>
            <div class="card-body">
                <p class="text-muted small mb-3">
                    <i class="bi bi-info-circle me-1"></i>
                    Click on the file below to download and view the video recording link inside.
                </p>
                <a href="{{ route('executive.submissions.download', $submission) }}" class="text-decoration-none">
                    <div class="border rounded p-3 d-flex align-items-center gap-3 bg-light hover-shadow" style="cursor: pointer;">
                        <i class="bi bi-{{ str_contains($submission->file_mime, 'pdf') ? 'file-earmark-pdf text-danger' : 'file-earmark-excel text-success' }} fs-1"></i>
                        <div class="flex-grow-1">
                            <div class="fw-semibold text-dark">{{ $submission->file_original_name }}</div>
                            <div class="text-muted small">{{ number_format($submission->file_size / 1024, 1) }} KB &mdash; {{ $submission->file_mime }}</div>
                        </div>
                        <div class="text-primary">
                            <i class="bi bi-download fs-4"></i>
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
