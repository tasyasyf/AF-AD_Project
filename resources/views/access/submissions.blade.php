<x-layouts.app title="Submissions (View)">

<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
    <div>
        <h5 class="fw-bold mb-1">Submissions</h5>
        <span class="badge bg-secondary"><i class="bi bi-eye"></i> Read-only view · granted by administrator</span>
    </div>
    <form method="GET" class="d-flex gap-2">
        <input type="text" name="search" value="{{ $search }}" class="form-control form-control-sm" style="max-width:220px" placeholder="Title / course / AF/AD...">
        <button class="btn btn-sm btn-primary"><i class="bi bi-search"></i></button>
    </form>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Title</th>
                        <th>AF/AD</th>
                        <th>Type</th>
                        <th>Course</th>
                        <th>Date</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($submissions as $submission)
                        <tr class="detail-trigger" data-detail="detail-{{ $submission->id }}" data-title="{{ $submission->title }}">
                            <td class="fw-semibold small">{{ $submission->title }}</td>
                            <td class="small">{{ $submission->profile?->full_name }}</td>
                            <td class="small">{{ $submission->type_label }}</td>
                            <td class="small">{{ $submission->course }}</td>
                            <td class="small">{{ ($submission->submission_date ?? $submission->created_at)?->format('d M Y') }}</td>
                            <td><x-status-badge :status="$submission->status" /></td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center text-muted py-4">No submissions found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="mt-3">{{ $submissions->links() }}</div>

{{-- Hidden detail blocks (rendered into the modal on row click) --}}
<div class="d-none">
    @foreach($submissions as $submission)
        <div id="detail-{{ $submission->id }}">
            <dl class="row mb-0">
                <dt class="col-sm-4 text-muted">Title</dt>
                <dd class="col-sm-8">{{ $submission->title }}</dd>
                <dt class="col-sm-4 text-muted">AF/AD</dt>
                <dd class="col-sm-8">{{ $submission->profile?->full_name ?? '—' }}</dd>
                <dt class="col-sm-4 text-muted">Type</dt>
                <dd class="col-sm-8">{{ $submission->type_label }}</dd>
                <dt class="col-sm-4 text-muted">Course</dt>
                <dd class="col-sm-8">{{ $submission->course ?: '—' }} {{ $submission->course_name ? '· '.$submission->course_name : '' }}</dd>
                @if($submission->tutorial_number)
                    <dt class="col-sm-4 text-muted">Tutorial No.</dt>
                    <dd class="col-sm-8">{{ $submission->tutorial_number }}</dd>
                @endif
                <dt class="col-sm-4 text-muted">Date</dt>
                <dd class="col-sm-8">{{ ($submission->submission_date ?? $submission->created_at)?->format('d M Y') }}</dd>
                @if($submission->claim_hours)
                    <dt class="col-sm-4 text-muted">Claim Hours</dt>
                    <dd class="col-sm-8">{{ number_format((float) $submission->claim_hours, 2) }}</dd>
                @endif
                @if($submission->total_amount)
                    <dt class="col-sm-4 text-muted">Amount</dt>
                    <dd class="col-sm-8">RM {{ number_format((float) $submission->total_amount, 2) }}</dd>
                @endif
                @if($submission->video_url)
                    <dt class="col-sm-4 text-muted">Video Link</dt>
                    <dd class="col-sm-8"><a href="{{ $submission->video_url }}" target="_blank" rel="noopener">{{ \Illuminate\Support\Str::limit($submission->video_url, 50) }}</a></dd>
                @endif
                <dt class="col-sm-4 text-muted">Status</dt>
                <dd class="col-sm-8">{{ ucfirst($submission->status) }}</dd>
            </dl>
        </div>
    @endforeach
</div>

@include('access.partials.detail-modal')

</x-layouts.app>
