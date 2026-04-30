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
                    <dt class="col-sm-3 text-muted">Description</dt>
                    <dd class="col-sm-9">{{ $submission->description ?? '—' }}</dd>
                    <dt class="col-sm-3 text-muted">Submitted</dt>
                    <dd class="col-sm-9">{{ $submission->created_at->format('d M Y H:i') }}</dd>
                </dl>
            </div>
        </div>

        <div class="card">
            <div class="card-header bg-white fw-semibold">Uploaded File</div>
            <div class="card-body">
                <div class="d-flex align-items-center gap-3">
                    <i class="bi bi-{{ str_contains($submission->file_mime, 'pdf') ? 'file-earmark-pdf text-danger' : 'file-earmark-excel text-success' }} fs-2"></i>
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
