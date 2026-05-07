<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="fw-bold mb-0">{{ $isEdit ? 'Edit Certificate' : 'Create Certificate' }}</h5>
    <a href="{{ route('admin.certificates.index') }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left me-1"></i> Back</a>
</div>

<form method="POST" action="{{ $action }}" enctype="multipart/form-data">
@csrf
@if($method) @method($method) @endif
<div class="row g-4">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header bg-white fw-semibold">Certificate Details</div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label fw-semibold">AF/AD <span class="text-danger">*</span></label>
                        <select name="profile_id" class="form-select @error('profile_id') is-invalid @enderror" required>
                            <option value="">Select AF/AD...</option>
                            @foreach($profiles as $profile)
                                <option value="{{ $profile->id }}" {{ old('profile_id', $certificate?->profile_id) == $profile->id ? 'selected' : '' }}>{{ $profile->full_name }}</option>
                            @endforeach
                        </select>
                        @error('profile_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-8">
                        <label class="form-label fw-semibold">Title <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title', $certificate?->title) }}" required>
                        @error('title') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Year <span class="text-danger">*</span></label>
                        <input type="number" name="year_obtained" min="1950" max="{{ date('Y') }}" class="form-control @error('year_obtained') is-invalid @enderror" value="{{ old('year_obtained', $certificate?->year_obtained) }}" required>
                        @error('year_obtained') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold">Issuing Institution <span class="text-danger">*</span></label>
                        <input type="text" name="issuing_institution" class="form-control @error('issuing_institution') is-invalid @enderror" value="{{ old('issuing_institution', $certificate?->issuing_institution) }}" required>
                        @error('issuing_institution') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold">File</label>
                        <input type="file" name="certificate_file" class="form-control @error('certificate_file') is-invalid @enderror" accept=".pdf,.jpg,.jpeg,.png">
                        @if($certificate?->file_original_name)<div class="form-text">Current: {{ $certificate->file_original_name }}</div>@endif
                        @error('certificate_file') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-12">
                        <div class="form-check">
                            <input type="hidden" name="is_verified" value="0">
                            <input class="form-check-input" type="checkbox" name="is_verified" value="1" id="is_verified" {{ old('is_verified', $certificate?->is_verified) ? 'checked' : '' }}>
                            <label class="form-check-label fw-semibold" for="is_verified">Verified</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header bg-white fw-semibold">{{ $isEdit ? 'Save' : 'Create' }}</div>
            <div class="card-body">
                <button class="btn btn-primary w-100" type="submit">{{ $isEdit ? 'Save Changes' : 'Create Certificate' }}</button>
            </div>
        </div>
    </div>
</div>
</form>
