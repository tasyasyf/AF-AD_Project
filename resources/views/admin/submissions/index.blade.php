<x-layouts.app title="Submissions">

<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="fw-bold mb-0">All Submissions</h5>
    <a href="{{ route('admin.submissions.create') }}" class="btn btn-primary btn-sm"><i class="bi bi-plus-lg me-1"></i> New Submission</a>
</div>

<div class="card mb-3">
    <div class="card-body py-2">
        <form method="GET" class="d-flex gap-2 flex-wrap">
            <input type="text" name="search" class="form-control form-control-sm" style="max-width:260px" placeholder="Search title / AFAD..." value="{{ request('search') }}">
            <select name="status" class="form-select form-select-sm" style="max-width:150px">
                <option value="">All Status</option>
                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="reviewed" {{ request('status') === 'reviewed' ? 'selected' : '' }}>Reviewed</option>
            </select>
            <button class="btn btn-sm btn-primary" type="submit"><i class="bi bi-search"></i> Filter</button>
            <a href="{{ route('admin.submissions.index') }}" class="btn btn-sm btn-outline-secondary">Reset</a>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>AF/AD</th>
                        <th>Title</th>
                        <th>Type</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($submissions as $submission)
                    <tr>
                        <td class="fw-semibold small">{{ $submission->profile->full_name }}</td>
                        <td>{{ $submission->title }}</td>
                        <td><span class="badge bg-light text-dark border">{{ $submission->type_label }}</span></td>
                        <td class="small">{{ ($submission->submission_date ?? $submission->created_at)->format('d M Y') }}</td>
                        <td><span class="badge {{ $submission->status === 'reviewed' ? 'bg-success' : 'bg-warning text-dark' }}">{{ ucfirst($submission->status) }}</span></td>
                        <td>
                            <div class="d-flex gap-1">
                                <a href="{{ route('admin.submissions.show', $submission) }}" class="btn btn-sm btn-outline-secondary">View</a>
                                <a href="{{ route('admin.submissions.edit', $submission) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                                <form method="POST" action="{{ route('admin.submissions.destroy', $submission) }}" onsubmit="return confirm('Delete this submission?');">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="text-center text-muted py-4">No submissions found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-3">{{ $submissions->links() }}</div>
    </div>
</div>

</x-layouts.app>
