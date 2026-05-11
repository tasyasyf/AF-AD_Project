<x-layouts.app title="My Submissions">

<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="fw-bold mb-0">My Submissions</h5>
    <a href="{{ route('afad.submissions.create') }}" class="btn btn-primary btn-sm">
        <i class="bi bi-plus-lg me-1"></i> New Submission
    </a>
</div>

<div class="card">
    <div class="card-body p-0">
        @if($submissions->isEmpty())
            <div class="text-center text-muted py-5">
                <i class="bi bi-camera-video fs-1 d-block mb-2"></i>
                No submissions yet. Click "New Submission" to upload your file.
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Title</th>
                            <th>Type</th>
                            <th>Course / Details</th>
                            <th>Amount</th>
                            <th>File</th>
                            <th>Submission Date</th>
                            <th>Status</th>
                            <th>Reviewed</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($submissions as $sub)
                        <tr>
                            <td class="fw-semibold">{{ $sub->title }}</td>
                            <td><span class="badge bg-light text-dark border">{{ $sub->type_label }}</span></td>
                            <td class="small text-muted">
                                <span class="fw-semibold text-dark">{{ $sub->course ?? '—' }}</span>
                                @if($sub->course_name)
                                    <br>{{ $sub->course_name }}
                                @endif
                                @if($sub->programme)
                                    <br>{{ $sub->programme }}
                                @endif
                                @if($sub->isVideoRecording())
                                    <br>Tutorial {{ $sub->tutorial_number ?? '—' }}
                                    @if($sub->video_duration_minutes)
                                        <br>{{ number_format($sub->video_duration_minutes, 2) }} min
                                    @endif
                                @elseif($sub->isQuestionBankAnswerSheet() && $sub->semester_intake)
                                    <br>{{ $sub->semester_intake }}
                                @endif
                            </td>
                            <td class="small">
                                @if($sub->total_amount || $sub->rate_per_hour)
                                    @if($sub->isVideoRecording())
                                        {{ number_format($sub->claim_hours ?? 0, 2) }} h<br>
                                    @else
                                        Rate<br>
                                    @endif
                                    RM {{ number_format($sub->total_amount ?? 0, 2) }}
                                @else
                                    —
                                @endif
                            </td>
                            <td class="small">
                                @if($sub->hasVideoLink())
                                    <i class="bi bi-link-45deg text-primary me-1"></i>
                                    <a href="{{ $sub->video_link }}" target="_blank" rel="noopener" class="text-decoration-none">Open Link</a>
                                @else
                                    <i class="bi bi-{{ str_starts_with($sub->file_mime, 'video/') ? 'camera-video text-danger' : (str_contains($sub->file_mime, 'pdf') ? 'file-earmark-pdf text-danger' : 'file-earmark-text text-primary') }} me-1"></i>
                                    {{ $sub->file_original_name }}
                                @endif
                            </td>
                            <td class="small text-muted">{{ ($sub->submission_date ?? $sub->created_at)->format('d M Y') }}</td>
                            <td>
                                @if($sub->status === 'reviewed')
                                    <span class="badge bg-success">Reviewed</span>
                                @else
                                    <span class="badge bg-warning text-dark">Pending</span>
                                @endif
                            </td>
                            <td class="small text-muted">{{ $sub->reviewed_at?->format('d M Y') ?? '—' }}</td>
                            <td>
                                <div class="d-flex gap-1">
                                    <a href="{{ route('afad.submissions.show', $sub) }}" class="btn btn-sm btn-outline-secondary">View</a>
                                    @if($sub->status !== 'reviewed')
                                        <form method="POST" action="{{ route('afad.submissions.destroy', $sub) }}" onsubmit="return confirm('Delete this submission?');" class="d-inline">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="p-3">{{ $submissions->links() }}</div>
        @endif
    </div>
</div>

</x-layouts.app>
