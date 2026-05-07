<x-layouts.app title="Certificates">

<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="fw-bold mb-0">All Certificates</h5>
    <a href="{{ route('admin.certificates.create') }}" class="btn btn-primary btn-sm"><i class="bi bi-plus-lg me-1"></i> New Certificate</a>
</div>

<div class="card mb-3">
    <div class="card-body py-2">
        <form method="GET" class="d-flex gap-2 flex-wrap">
            <input type="text" name="search" class="form-control form-control-sm" style="max-width:260px" placeholder="Search certificate / AFAD..." value="{{ request('search') }}">
            <button class="btn btn-sm btn-primary" type="submit"><i class="bi bi-search"></i> Filter</button>
            <a href="{{ route('admin.certificates.index') }}" class="btn btn-sm btn-outline-secondary">Reset</a>
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
                        <th>Institution</th>
                        <th>Year</th>
                        <th>Verified</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($certificates as $certificate)
                    <tr>
                        <td class="small fw-semibold">{{ $certificate->profile->full_name }}</td>
                        <td>{{ $certificate->title }}</td>
                        <td class="small">{{ $certificate->issuing_institution }}</td>
                        <td>{{ $certificate->year_obtained }}</td>
                        <td>
                            @if($certificate->is_verified)<span class="badge bg-success">Verified</span>
                            @else <span class="badge bg-warning text-dark">Pending</span>@endif
                        </td>
                        <td>
                            <div class="d-flex gap-1">
                                <a href="{{ route('admin.certificates.edit', $certificate) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                                <form method="POST" action="{{ route('admin.certificates.destroy', $certificate) }}" onsubmit="return confirm('Delete this certificate?');">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="text-center text-muted py-4">No certificates found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-3">{{ $certificates->links() }}</div>
    </div>
</div>

</x-layouts.app>
