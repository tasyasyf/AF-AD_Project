<x-layouts.app title="Submissions">

<h5 class="fw-bold mb-4">AF/AD Submissions</h5>

<div class="card mb-3">
    <div class="card-body py-2">
        <form method="GET" class="d-flex gap-2 flex-wrap">
            <input type="text" name="search" class="form-control form-control-sm" style="max-width:240px"
                placeholder="Search title or AF/AD name..." value="{{ request('search') }}">
            <select name="status" class="form-select form-select-sm" style="max-width:160px">
                <option value="">All Statuses</option>
                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="reviewed" {{ request('status') === 'reviewed' ? 'selected' : '' }}>Reviewed</option>
            </select>
            <button class="btn btn-sm btn-primary" type="submit"><i class="bi bi-search"></i> Filter</button>
            <a href="{{ route('executive.submissions.index') }}" class="btn btn-sm btn-outline-secondary">Reset</a>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body p-0">
        @if($submissions->isEmpty())
            <div class="text-center text-muted py-5">
                <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                No submissions found.
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>AF/AD</th>
                            <th>Title</th>
                            <th>Type</th>
                            <th>Tutorial / Duration</th>
                            <th>File</th>
                            <th>Submission Date</th>
                            <th>Status</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($submissions as $sub)
                        <tr>
                            <td class="fw-semibold">{{ $sub->profile->full_name }}</td>
                            <td>{{ $sub->title }}</td>
                            <td><span class="badge bg-light text-dark border">{{ $sub->type_label }}</span></td>
                            <td class="small text-muted">
                                @if($sub->isVideoRecording())
                                    Tutorial {{ $sub->tutorial_number ?? '—' }}<br>
                                    {{ $sub->video_duration_minutes ? number_format($sub->video_duration_minutes, 2) . ' min' : '—' }}
                                @elseif($sub->isQuestionBankAnswerSheet())
                                    {{ $sub->semester_intake ?? '—' }}<br>
                                    {{ $sub->course ?? '—' }}
                                @elseif($sub->isMarkEntryForms())
                                    {{ $sub->course ?? '—' }}<br>
                                    {{ $sub->programme ?? '—' }}
                                @else
                                    —
                                @endif
                            </td>
                            <td class="small">
                                <a href="{{ route('executive.submissions.download', $sub) }}" class="text-decoration-none">
                                    <i class="bi bi-{{ str_starts_with($sub->file_mime, 'video/') ? 'camera-video text-danger' : (str_contains($sub->file_mime, 'pdf') ? 'file-earmark-pdf text-danger' : 'file-earmark-text text-primary') }} me-1"></i>
                                    {{ $sub->file_original_name }}
                                </a>
                            </td>
                            <td class="small text-muted">{{ ($sub->submission_date ?? $sub->created_at)->format('d M Y') }}</td>
                            <td>
                                @if($sub->status === 'reviewed')
                                    <span class="badge bg-success">Reviewed</span>
                                @else
                                    <span class="badge bg-warning text-dark">Pending</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('executive.submissions.show', $sub) }}" class="btn btn-sm btn-outline-secondary">View</a>
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
