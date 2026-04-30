<x-layouts.app title="Edit Profile">

<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="fw-bold mb-0">Edit Profile: {{ $profile->full_name }}</h5>
    <a href="{{ route('admin.profiles.show', $profile) }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i> Back
    </a>
</div>

<form method="POST" action="{{ route('admin.profiles.update', $profile) }}">
@csrf @method('PUT')

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card mb-4">
            <div class="card-header bg-white fw-semibold">Personal Information</div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Full Name <span class="text-danger">*</span></label>
                        <input type="text" name="full_name" class="form-control @error('full_name') is-invalid @enderror"
                            value="{{ old('full_name', $profile->full_name) }}" required>
                        @error('full_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">IC Number <span class="text-danger">*</span></label>
                        <input type="text" name="ic_number" class="form-control @error('ic_number') is-invalid @enderror"
                            value="{{ old('ic_number', $profile->ic_number) }}" required>
                        @error('ic_number') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Phone <span class="text-danger">*</span></label>
                        <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror"
                            value="{{ old('phone', $profile->phone) }}" required>
                        @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Contact Email <span class="text-danger">*</span></label>
                        <input type="email" name="contact_email" class="form-control @error('contact_email') is-invalid @enderror"
                            value="{{ old('contact_email', $profile->contact_email) }}" required>
                        @error('contact_email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold">Address <span class="text-danger">*</span></label>
                        <textarea name="address" class="form-control @error('address') is-invalid @enderror" rows="3" required>{{ old('address', $profile->address) }}</textarea>
                        @error('address') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header bg-white fw-semibold">Qualification</div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-8">
                        <label class="form-label fw-semibold">Qualification <span class="text-danger">*</span></label>
                        <input type="text" name="qualification" class="form-control @error('qualification') is-invalid @enderror"
                            value="{{ old('qualification', $profile->qualification) }}" required>
                        @error('qualification') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Level <span class="text-danger">*</span></label>
                        <select name="qualification_level" class="form-select @error('qualification_level') is-invalid @enderror" required>
                            @foreach(['diploma','degree','masters','phd','professional'] as $level)
                                <option value="{{ $level }}" {{ old('qualification_level', $profile->qualification_level) === $level ? 'selected' : '' }}>{{ ucfirst($level) }}</option>
                            @endforeach
                        </select>
                        @error('qualification_level') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold">Specialisation</label>
                        <input type="text" name="specialisation" class="form-control"
                            value="{{ old('specialisation', $profile->specialisation) }}">
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header bg-white fw-semibold">Bank Account Information</div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Bank Name <span class="text-danger">*</span></label>
                        <input type="text" name="bank_name" class="form-control @error('bank_name') is-invalid @enderror"
                            value="{{ old('bank_name', $profile->bank_name) }}" required>
                        @error('bank_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Account Number <span class="text-danger">*</span></label>
                        <input type="text" name="bank_account_number" class="form-control @error('bank_account_number') is-invalid @enderror"
                            value="{{ old('bank_account_number', $profile->bank_account_number) }}" required>
                        @error('bank_account_number') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold">Account Holder <span class="text-danger">*</span></label>
                        <input type="text" name="bank_account_holder" class="form-control @error('bank_account_holder') is-invalid @enderror"
                            value="{{ old('bank_account_holder', $profile->bank_account_holder) }}" required>
                        @error('bank_account_holder') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card mb-4">
            <div class="card-header bg-white fw-semibold">Status</div>
            <div class="card-body">
                <label class="form-label fw-semibold">Profile Status <span class="text-danger">*</span></label>
                <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                    @foreach(['pending','verified','rejected'] as $status)
                        <option value="{{ $status }}" {{ old('status', $profile->status) === $status ? 'selected' : '' }}>{{ ucfirst($status) }}</option>
                    @endforeach
                </select>
                @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
        </div>

        <div class="card">
            <div class="card-header bg-white fw-semibold">Save Changes</div>
            <div class="card-body">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-save me-1"></i> Save Changes
                </button>
            </div>
        </div>
    </div>
</div>

</form>
</x-layouts.app>
