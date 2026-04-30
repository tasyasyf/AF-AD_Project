<x-layouts.app title="Edit Claim">

<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="fw-bold mb-0">Edit Claim: {{ $claim->claim_reference }}</h5>
    <a href="{{ route('admin.claims.show', $claim) }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i> Back
    </a>
</div>

<form method="POST" action="{{ route('admin.claims.update', $claim) }}">
@csrf @method('PUT')

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card mb-4">
            <div class="card-header bg-white fw-semibold">Claim Details</div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Claim Type <span class="text-danger">*</span></label>
                        <select name="claim_type" class="form-select @error('claim_type') is-invalid @enderror" required>
                            @foreach(['teaching','marking','module_development','consultation'] as $type)
                                <option value="{{ $type }}" {{ old('claim_type', $claim->claim_type) === $type ? 'selected' : '' }}>{{ ucfirst(str_replace('_',' ',$type)) }}</option>
                            @endforeach
                        </select>
                        @error('claim_type') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Status <span class="text-danger">*</span></label>
                        <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                            @foreach(['draft','submitted','under_review','approved','returned','rejected'] as $status)
                                <option value="{{ $status }}" {{ old('status', $claim->status) === $status ? 'selected' : '' }}>{{ ucfirst(str_replace('_',' ',$status)) }}</option>
                            @endforeach
                        </select>
                        @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Period From <span class="text-danger">*</span></label>
                        <input type="date" name="period_from" class="form-control @error('period_from') is-invalid @enderror"
                            value="{{ old('period_from', $claim->period_from->format('Y-m-d')) }}" required>
                        @error('period_from') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Period To <span class="text-danger">*</span></label>
                        <input type="date" name="period_to" class="form-control @error('period_to') is-invalid @enderror"
                            value="{{ old('period_to', $claim->period_to->format('Y-m-d')) }}" required>
                        @error('period_to') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Total Hours <span class="text-danger">*</span></label>
                        <input type="number" name="total_hours" step="0.01" class="form-control @error('total_hours') is-invalid @enderror"
                            value="{{ old('total_hours', $claim->total_hours) }}" min="0" required>
                        @error('total_hours') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Rate per Hour (RM) <span class="text-danger">*</span></label>
                        <input type="number" name="rate_per_hour" step="0.01" class="form-control @error('rate_per_hour') is-invalid @enderror"
                            value="{{ old('rate_per_hour', $claim->rate_per_hour) }}" min="0" required>
                        @error('rate_per_hour') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold">Executive Remarks</label>
                        <textarea name="executive_remarks" class="form-control @error('executive_remarks') is-invalid @enderror" rows="3">{{ old('executive_remarks', $claim->executive_remarks) }}</textarea>
                        @error('executive_remarks') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card mb-4">
            <div class="card-header bg-white fw-semibold">Claim Info</div>
            <div class="card-body small">
                <div><strong>AF/AD:</strong> {{ $claim->profile->full_name }}</div>
                <div><strong>Course:</strong> {{ $claim->appointment->course_code }}</div>
                <div><strong>Reference:</strong> {{ $claim->claim_reference }}</div>
                <hr>
                <div class="text-muted">Total amount will be recalculated as <strong>hours × rate</strong> on save.</div>
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
