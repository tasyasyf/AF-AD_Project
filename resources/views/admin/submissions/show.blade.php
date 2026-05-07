<x-layouts.app title="Submission Detail">

<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="fw-bold mb-0">{{ $submission->title }}</h5>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.submissions.edit', $submission) }}" class="btn btn-primary btn-sm">Edit</a>
        <a href="{{ route('admin.submissions.index') }}" class="btn btn-outline-secondary btn-sm">Back</a>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card mb-4">
            <div class="card-header bg-white fw-semibold">Submission Details</div>
            <div class="card-body">
                <dl class="row mb-0">
                    <dt class="col-sm-3 text-muted">AF/AD</dt><dd class="col-sm-9">{{ $submission->profile->full_name }}</dd>
                    <dt class="col-sm-3 text-muted">Type</dt><dd class="col-sm-9">{{ $submission->type_label }}</dd>
                    <dt class="col-sm-3 text-muted">Date</dt><dd class="col-sm-9">{{ ($submission->submission_date ?? $submission->created_at)->format('d M Y') }}</dd>
                    @if($submission->isVideoRecording())
                        <dt class="col-sm-3 text-muted">Tutorial</dt><dd class="col-sm-9">Tutorial {{ $submission->tutorial_number ?? '—' }}</dd>
                        <dt class="col-sm-3 text-muted">Duration</dt><dd class="col-sm-9">{{ $submission->video_duration_minutes ? number_format($submission->video_duration_minutes, 2) . ' minutes' : '—' }}</dd>
                    @endif
                    @if($submission->isQuestionBankAnswerSheet())
                        <dt class="col-sm-3 text-muted">Semester Intake</dt><dd class="col-sm-9">{{ $submission->semester_intake ?? '—' }}</dd>
                        <dt class="col-sm-3 text-muted">Course</dt><dd class="col-sm-9">{{ $submission->course ?? '—' }}</dd>
                        <dt class="col-sm-3 text-muted">Programme</dt><dd class="col-sm-9">{{ $submission->programme ?? '—' }}</dd>
                    @endif
                    @if($submission->isMarkEntryForms())
                        <dt class="col-sm-3 text-muted">Course Code</dt><dd class="col-sm-9">{{ $submission->course ?? '—' }}</dd>
                        <dt class="col-sm-3 text-muted">Course Name</dt><dd class="col-sm-9">{{ $submission->course_name ?? '—' }}</dd>
                        <dt class="col-sm-3 text-muted">Programme</dt><dd class="col-sm-9">{{ $submission->programme ?? '—' }}</dd>
                    @endif
                    <dt class="col-sm-3 text-muted">Description</dt><dd class="col-sm-9">{{ $submission->description ?? '—' }}</dd>
                    <dt class="col-sm-3 text-muted">Status</dt><dd class="col-sm-9">{{ ucfirst($submission->status) }}</dd>
                    <dt class="col-sm-3 text-muted">Remarks</dt><dd class="col-sm-9">{{ $submission->executive_remarks ?? '—' }}</dd>
                </dl>
            </div>
        </div>
        <div class="card">
            <div class="card-header bg-white fw-semibold">File</div>
            <div class="card-body d-flex align-items-center justify-content-between gap-3">
                <div>
                    <div class="fw-semibold">{{ $submission->file_original_name }}</div>
                    <div class="text-muted small">{{ number_format($submission->file_size / 1024, 1) }} KB</div>
                </div>
                <a href="{{ route('admin.submissions.download', $submission) }}" class="btn btn-outline-primary">Download</a>
            </div>
        </div>
    </div>
</div>

</x-layouts.app>
