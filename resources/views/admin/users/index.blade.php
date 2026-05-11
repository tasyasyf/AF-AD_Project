<x-layouts.app title="Users & Roles">

<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="fw-bold mb-0">Users & Roles</h5>
    <a href="{{ route('admin.users.create') }}" class="btn btn-primary btn-sm">
        <i class="bi bi-person-plus me-1"></i> New User
    </a>
</div>

<div class="card mb-3">
    <div class="card-body py-2">
        <form method="GET" class="d-flex gap-2 flex-wrap">
            <input type="text" name="search" class="form-control form-control-sm" style="max-width:240px"
                placeholder="Search name / email..." value="{{ request('search') }}">
            <select name="role" class="form-select form-select-sm" style="max-width:180px">
                <option value="">All Roles</option>
                <option value="afad" {{ request('role') === 'afad' ? 'selected' : '' }}>AF/AD</option>
                <option value="executive" {{ request('role') === 'executive' ? 'selected' : '' }}>School Executive</option>
                <option value="pc" {{ request('role') === 'pc' ? 'selected' : '' }}>Program Coordinator</option>
                <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Admin</option>
            </select>
            <button class="btn btn-sm btn-primary" type="submit"><i class="bi bi-search"></i> Filter</button>
            <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-outline-secondary">Reset</a>
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
                        <th>Email</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Created</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                    <tr>
                        <td class="fw-semibold">{{ $user->name }}</td>
                        <td class="small">{{ $user->email }}</td>
                        <td>
                            <span class="badge bg-light text-dark border">
                                @if($user->role === 'executive')
                                    School Executive
                                @elseif($user->role === 'pc')
                                    Program Coordinator
                                @else
                                    {{ strtoupper($user->role) }}
                                @endif
                            </span>
                        </td>
                        <td>
                            @if($user->is_active)<span class="badge bg-success">Active</span>
                            @else <span class="badge bg-secondary">Inactive</span>@endif
                        </td>
                        <td class="small text-muted">{{ $user->created_at->format('d M Y') }}</td>
                        <td>
                            <div class="d-flex gap-1">
                                <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                                @if($user->id !== auth()->id())
                                    <form method="POST" action="{{ route('admin.users.destroy', $user) }}" onsubmit="return confirm('Delete this user? Related AF/AD profile data will also be deleted if this is an AF/AD account.');">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger">Delete</button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="text-center text-muted py-4">No users found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-3">{{ $users->links() }}</div>
    </div>
</div>

</x-layouts.app>
