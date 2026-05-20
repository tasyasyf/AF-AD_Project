<x-layouts.app title="Users & Roles">

<style>
    .user-list-avatar {
        width: 42px;
        height: 42px;
        border-radius: 50%;
        object-fit: cover;
        border: 1px solid var(--portal-border);
        background: var(--portal-soft);
        color: var(--portal-red);
        flex: 0 0 42px;
    }
    .user-list-avatar-placeholder {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
    }
    .user-profile-photo-large {
        width: 150px;
        height: 150px;
        border-radius: 50%;
        object-fit: cover;
        border: 1px solid var(--portal-border);
        background: var(--portal-soft);
        color: var(--portal-red);
    }
    .user-profile-photo-large.user-list-avatar-placeholder {
        font-size: 3.5rem;
    }
    .user-profile-modal-row,
    .user-profile-modal-cell {
        display: contents;
    }
</style>

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
                    @php
                        $roleLabel = match ($user->role) {
                            'executive' => 'School Executive',
                            'pc' => 'Program Coordinator',
                            default => strtoupper($user->role),
                        };
                    @endphp
                    <tr>
                        <td>
                            <div class="d-flex align-items-center gap-3">
                                @if($user->profile_photo_path)
                                    <img src="{{ route('profile-photo.show', $user) }}" alt="{{ $user->name }}" class="user-list-avatar">
                                @else
                                    <span class="user-list-avatar user-list-avatar-placeholder" aria-label="No profile photo">
                                        <i class="bi bi-person"></i>
                                    </span>
                                @endif
                                <span class="fw-semibold">{{ $user->name }}</span>
                            </div>
                        </td>
                        <td class="small">{{ $user->email }}</td>
                        <td>
                            <span class="badge bg-light text-dark border">
                                {{ $roleLabel }}
                            </span>
                        </td>
                        <td>
                            @if($user->is_active)<span class="badge bg-success">Active</span>
                            @else <span class="badge bg-secondary">Inactive</span>@endif
                        </td>
                        <td class="small text-muted">{{ $user->created_at->format('d M Y') }}</td>
                        <td>
                            <div class="d-flex gap-1">
                                <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#userProfileModal{{ $user->id }}">
                                    View Profile
                                </button>
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
                    <tr class="user-profile-modal-row">
                        <td colspan="6" class="user-profile-modal-cell">
                            <div class="modal fade" id="userProfileModal{{ $user->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-xl modal-dialog-scrollable">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Profile Detail: {{ $user->name }}</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="row g-4">
                                                <div class="col-lg-4 text-center">
                                                    @if($user->profile_photo_path)
                                                        <img src="{{ route('profile-photo.show', $user) }}" alt="{{ $user->name }}" class="user-profile-photo-large mb-3">
                                                    @else
                                                        <span class="user-profile-photo-large user-list-avatar-placeholder mb-3">
                                                            <i class="bi bi-person"></i>
                                                        </span>
                                                    @endif
                                                    <div class="fw-semibold">{{ $user->name }}</div>
                                                    <div class="text-muted small">{{ $user->email }}</div>
                                                    <div class="mt-2">
                                                        <span class="badge bg-light text-dark border">{{ $roleLabel }}</span>
                                                        @if($user->is_active)
                                                            <span class="badge bg-success">Active</span>
                                                        @else
                                                            <span class="badge bg-secondary">Inactive</span>
                                                        @endif
                                                    </div>
                                                </div>

                                                <div class="col-lg-8">
                                                    <div class="card mb-3">
                                                        <div class="card-header bg-white fw-semibold">Account Data</div>
                                                        <div class="card-body">
                                                            <dl class="row mb-0">
                                                                <dt class="col-sm-4 text-muted">Login Name</dt>
                                                                <dd class="col-sm-8">{{ $user->name }}</dd>
                                                                <dt class="col-sm-4 text-muted">Login Email</dt>
                                                                <dd class="col-sm-8">{{ $user->email }}</dd>
                                                                <dt class="col-sm-4 text-muted">Role</dt>
                                                                <dd class="col-sm-8">{{ $roleLabel }}</dd>
                                                                <dt class="col-sm-4 text-muted">Status</dt>
                                                                <dd class="col-sm-8">{{ $user->is_active ? 'Active' : 'Inactive' }}</dd>
                                                                <dt class="col-sm-4 text-muted">Created</dt>
                                                                <dd class="col-sm-8">{{ $user->created_at->format('d M Y H:i') }}</dd>
                                                            </dl>
                                                        </div>
                                                    </div>

                                                    @if($user->profile)
                                                        <div class="card mb-3">
                                                            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                                                                <span class="fw-semibold">Detailed Profile Data</span>
                                                                <x-status-badge :status="$user->profile->status" />
                                                            </div>
                                                            <div class="card-body">
                                                                <dl class="row mb-0">
                                                                    <dt class="col-sm-4 text-muted">Full Name</dt>
                                                                    <dd class="col-sm-8">{{ $user->profile->full_name }}</dd>
                                                                    <dt class="col-sm-4 text-muted">IC Number</dt>
                                                                    <dd class="col-sm-8">{{ $user->profile->ic_number }}</dd>
                                                                    <dt class="col-sm-4 text-muted">Date of Birth</dt>
                                                                    <dd class="col-sm-8">{{ $user->profile->date_of_birth?->format('d M Y') ?? '-' }}</dd>
                                                                    <dt class="col-sm-4 text-muted">Gender</dt>
                                                                    <dd class="col-sm-8">{{ $user->profile->gender ? ucfirst($user->profile->gender) : '-' }}</dd>
                                                                    <dt class="col-sm-4 text-muted">Phone</dt>
                                                                    <dd class="col-sm-8">{{ $user->profile->phone }}</dd>
                                                                    <dt class="col-sm-4 text-muted">Contact Email</dt>
                                                                    <dd class="col-sm-8">{{ $user->profile->contact_email }}</dd>
                                                                    <dt class="col-sm-4 text-muted">Address</dt>
                                                                    <dd class="col-sm-8">{{ $user->profile->address }}</dd>
                                                                    <dt class="col-sm-4 text-muted">Qualification</dt>
                                                                    <dd class="col-sm-8">{{ $user->profile->qualification }} ({{ ucfirst($user->profile->qualification_level) }})</dd>
                                                                    <dt class="col-sm-4 text-muted">Specialisation</dt>
                                                                    <dd class="col-sm-8">{{ $user->profile->specialisation ?? '-' }}</dd>
                                                                    <dt class="col-sm-4 text-muted">Area of Expertise</dt>
                                                                    <dd class="col-sm-8">{{ $user->profile->area_of_expertise ?? '-' }}</dd>
                                                                    <dt class="col-sm-4 text-muted">Bank</dt>
                                                                    <dd class="col-sm-8">{{ $user->profile->bank_name }} - {{ $user->profile->bank_account_number }}</dd>
                                                                    <dt class="col-sm-4 text-muted">Account Holder</dt>
                                                                    <dd class="col-sm-8">{{ $user->profile->bank_account_holder }}</dd>
                                                                    <dt class="col-sm-4 text-muted">Verified By</dt>
                                                                    <dd class="col-sm-8">{{ $user->profile->verifier?->name ?? '-' }}</dd>
                                                                    <dt class="col-sm-4 text-muted">Verified At</dt>
                                                                    <dd class="col-sm-8">{{ $user->profile->verified_at?->format('d M Y H:i') ?? '-' }}</dd>
                                                                    @if($user->profile->status === 'rejected')
                                                                        <dt class="col-sm-4 text-muted">Rejection Reason</dt>
                                                                        <dd class="col-sm-8">{{ $user->profile->rejection_reason ?? '-' }}</dd>
                                                                    @endif
                                                                </dl>
                                                            </div>
                                                        </div>
                                                    @else
                                                        <div class="alert alert-light border mb-0">
                                                            No detailed profile record has been registered for this user.
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            @if($user->profile)
                                                <a href="{{ route('admin.profiles.show', $user->profile) }}" class="btn btn-outline-primary">Open Full Profile</a>
                                            @endif
                                            <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-primary">Edit User</a>
                                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                                        </div>
                                    </div>
                                </div>
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
