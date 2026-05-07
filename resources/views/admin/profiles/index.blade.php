<x-layouts.app title="AF/AD Profiles">

<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="fw-bold mb-0">AF/AD Profiles</h5>
    <a href="{{ route('admin.profiles.create') }}" class="btn btn-primary btn-sm">
        <i class="bi bi-person-plus me-1"></i> New Profile
    </a>
</div>

<div class="card mb-3">
    <div class="card-body py-2">
        <form method="GET" class="d-flex gap-2 flex-wrap">
            <input type="text" name="search" class="form-control form-control-sm" style="max-width:220px"
                placeholder="Search name / IC..." value="{{ request('search') }}">
            <select name="status" class="form-select form-select-sm" style="max-width:160px">
                <option value="">All Statuses</option>
                @foreach(['pending','verified','rejected'] as $s)
                    <option value="{{ $s }}" {{ request('status') === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                @endforeach
            </select>
            <button class="btn btn-sm btn-primary" type="submit"><i class="bi bi-search"></i> Filter</button>
            <a href="{{ route('admin.profiles.index') }}" class="btn btn-sm btn-outline-secondary">Reset</a>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Name</th>
                        <th>IC Number</th>
                        <th>Qualification</th>
                        <th>Email</th>
                        <th>Status</th>
                        <th>Registered</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($profiles as $profile)
                    <tr>
                        <td class="fw-semibold">{{ $profile->full_name }}</td>
                        <td>{{ $profile->ic_number }}</td>
                        <td class="small">{{ ucfirst($profile->qualification_level) }}</td>
                        <td class="small">{{ $profile->contact_email }}</td>
                        <td><x-status-badge :status="$profile->status" /></td>
                        <td class="small text-muted">{{ $profile->created_at->format('d M Y') }}</td>
                        <td>
                            <div class="d-flex gap-1">
                                <a href="{{ route('admin.profiles.show', $profile) }}" class="btn btn-sm btn-outline-secondary">View</a>
                                <a href="{{ route('admin.profiles.edit', $profile) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                                <form method="POST" action="{{ route('admin.profiles.destroy', $profile) }}" onsubmit="return confirm('Delete this profile? This cannot be undone.');" class="d-inline">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="text-center text-muted py-4">No profiles found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-3">{{ $profiles->links() }}</div>
    </div>
</div>

</x-layouts.app>
