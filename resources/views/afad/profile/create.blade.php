<x-layouts.app title="Register Profile">

<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="fw-bold mb-0">Register Your Profile</h5>
</div>

<form method="POST" action="{{ route('afad.profile.store') }}">
@csrf

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card mb-4">
            <div class="card-header bg-white fw-semibold">Personal Information</div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Full Name <span class="text-danger">*</span></label>
                        <input type="text" name="full_name" class="form-control @error('full_name') is-invalid @enderror"
                            value="{{ old('full_name') }}" required>
                        @error('full_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">IC Number <span class="text-danger">*</span></label>
                        <input type="text" name="ic_number" class="form-control @error('ic_number') is-invalid @enderror"
                            value="{{ old('ic_number') }}" placeholder="e.g. 850101-01-1234" required>
                        @error('ic_number') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Phone <span class="text-danger">*</span></label>
                        <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror"
                            value="{{ old('phone') }}" placeholder="e.g. 012-3456789" required>
                        @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Contact Email <span class="text-danger">*</span></label>
                        <input type="email" name="contact_email" class="form-control @error('contact_email') is-invalid @enderror"
                            value="{{ old('contact_email') }}" required>
                        @error('contact_email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold">Address <span class="text-danger">*</span></label>
                        <textarea name="address" class="form-control @error('address') is-invalid @enderror"
                            rows="3" required>{{ old('address') }}</textarea>
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
                            value="{{ old('qualification') }}" placeholder="e.g. Bachelor of Education (TESL)" required>
                        @error('qualification') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Level <span class="text-danger">*</span></label>
                        <select name="qualification_level" class="form-select @error('qualification_level') is-invalid @enderror" required>
                            <option value="">Select...</option>
                            @foreach(['diploma','degree','masters','phd','professional'] as $level)
                                <option value="{{ $level }}" {{ old('qualification_level') === $level ? 'selected' : '' }}>
                                    {{ ucfirst($level) }}
                                </option>
                            @endforeach
                        </select>
                        @error('qualification_level') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold">Specialisation</label>
                        <input type="text" name="specialisation" class="form-control @error('specialisation') is-invalid @enderror"
                            value="{{ old('specialisation') }}" placeholder="Optional">
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
                            value="{{ old('bank_name') }}" placeholder="e.g. Maybank" required>
                        @error('bank_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Account Number <span class="text-danger">*</span></label>
                        <input type="text" name="bank_account_number" class="form-control @error('bank_account_number') is-invalid @enderror"
                            value="{{ old('bank_account_number') }}" required>
                        @error('bank_account_number') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold">Account Holder Name <span class="text-danger">*</span></label>
                        <input type="text" name="bank_account_holder" class="form-control @error('bank_account_holder') is-invalid @enderror"
                            value="{{ old('bank_account_holder') }}" required>
                        @error('bank_account_holder') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card">
            <div class="card-header bg-white fw-semibold">Submit Registration</div>
            <div class="card-body">
                <p class="text-muted small">After registration, your profile will be reviewed and verified by the School Executive before you can submit claims.</p>
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-send-fill me-1"></i> Submit Profile
                </button>
            </div>
        </div>
    </div>
</div>

</form>
</x-layouts.app>
