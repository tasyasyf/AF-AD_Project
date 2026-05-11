<div class="card mb-4">
    <div class="card-header bg-white fw-semibold">Uploaded Submissions</div>
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
