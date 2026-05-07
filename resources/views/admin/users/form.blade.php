<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="fw-bold mb-0">{{ $isEdit ? 'Edit User' : 'Create User' }}</h5>
    <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i> Back
    </a>
</div>

<form method="POST" action="{{ $action }}">
@csrf
@if($method) @method($method) @endif

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header bg-white fw-semibold">Account Details</div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                            value="{{ old('name', $user?->name) }}" required>
                        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Email <span class="text-danger">*</span></label>
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                            value="{{ old('email', $user?->email) }}" required>
                        @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Role <span class="text-danger">*</span></label>
                        <select name="role" class="form-select @error('role') is-invalid @enderror" required>
                            @php($selectedRole = old('role', $user?->role ?? request('role', 'afad')))
                            <option value="afad" {{ $selectedRole === 'afad' ? 'selected' : '' }}>AF/AD</option>
                            <option value="executive" {{ $selectedRole === 'executive' ? 'selected' : '' }}>School Executive</option>
                            <option value="admin" {{ $selectedRole === 'admin' ? 'selected' : '' }}>Admin</option>
                        </select>
                        @error('role') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Password {{ $isEdit ? '' : '*' }}</label>
                        <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" {{ $isEdit ? '' : 'required' }}>
                        @if($isEdit)<div class="form-text">Leave blank to keep current password.</div>@endif
                        @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-12">
                        <div class="form-check">
                            <input type="hidden" name="is_active" value="0">
                            <input class="form-check-input" type="checkbox" name="is_active" value="1" id="is_active"
                                {{ old('is_active', $user?->is_active ?? true) ? 'checked' : '' }}>
                            <label class="form-check-label fw-semibold" for="is_active">Active account</label>
                        </div>
                    </div>
                    @if($isEdit && $user?->role === 'afad')
                        <div class="col-12">
                            <div class="alert alert-info small mb-0">
                                AF/AD detailed profile data is managed from the AF/AD Profiles page.
                            </div>
                        </div>
                    @elseif($isEdit && $user?->role === 'executive')
                        <div class="col-12">
                            <div class="alert alert-info small mb-0">
                                This account controls access to the School Executive portal.
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card">
            <div class="card-header bg-white fw-semibold">{{ $isEdit ? 'Save' : 'Create' }}</div>
            <div class="card-body">
                <button type="submit" class="btn btn-primary w-100">
                    {{ $isEdit ? 'Save Changes' : 'Create User' }}
                </button>
            </div>
        </div>
    </div>
</div>
</form>
