<x-layouts.app title="Permissions">

<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
    <div>
        <h5 class="fw-bold mb-1">Permissions</h5>
        <div class="text-muted small">Grant read-only "Additional Access" so a user can view functions outside their role.</div>
    </div>
    <form method="GET" class="d-flex gap-2 flex-wrap">
        <select name="role" class="form-select form-select-sm" style="max-width:160px" onchange="this.form.submit()">
            <option value="">All Roles</option>
            @foreach(['afad'=>'AF/AD','executive'=>'School Executive','pc'=>'Program Coordinator'] as $value=>$label)
                <option value="{{ $value }}" {{ request('role')===$value ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
        </select>
        <input type="text" name="search" value="{{ request('search') }}" class="form-control form-control-sm" style="max-width:200px" placeholder="Name / email...">
        <button class="btn btn-sm btn-primary"><i class="bi bi-search"></i></button>
    </form>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th>User</th>
                        <th>Role</th>
                        <th>Additional Access</th>
                        <th>Granted Functions</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                        @php $count = count($user->grantedPermissions()); @endphp
                        <tr>
                            <td>
                                <div class="fw-semibold">{{ $user->name }}</div>
                                <div class="text-muted small">{{ $user->email }}</div>
                            </td>
                            <td>
                                <span class="badge bg-secondary text-uppercase">{{ $user->role }}</span>
                            </td>
                            <td>
                                @if(!$user->additional_access_enabled)
                                    <span class="badge bg-danger">Master OFF</span>
                                @elseif($count > 0)
                                    <span class="badge bg-success">ON</span>
                                @else
                                    <span class="badge bg-light text-dark border">None</span>
                                @endif
                            </td>
                            <td class="small text-muted">{{ $count }} function{{ $count === 1 ? '' : 's' }}</td>
                            <td class="text-end">
                                <a href="{{ route('admin.permissions.edit', $user) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-sliders"></i> Manage
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center text-muted py-4">No users found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="mt-3">{{ $users->links() }}</div>

</x-layouts.app>
