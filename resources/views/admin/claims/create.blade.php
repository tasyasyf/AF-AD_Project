<x-layouts.app title="Create Claim">

<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="fw-bold mb-0">Create Claim</h5>
    <a href="{{ route('admin.claims.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i> Back
    </a>
</div>

<form method="POST" action="{{ route('admin.claims.store') }}">
@csrf

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card mb-4">
            <div class="card-header bg-white fw-semibold">Claim Details</div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label fw-semibold">Appointment / AFAD <span class="text-danger">*</span></label>
                        <select name="appointment_id" class="form-select @error('appointment_id') is-invalid @enderror" required>
                            <option value="">Select appointment...</option>
                            @foreach($appointments as $appointment)
                                <option value="{{ $appointment->id }}" {{ old('appointment_id') == $appointment->id ? 'selected' : '' }}>
                                    {{ $appointment->profile->full_name }} - {{ $appointment->course_code }} / {{ $appointment->course_name }} ({{ $appointment->semester }})
                                </option>
                            @endforeach
                        </select>
                        @error('appointment_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Claim Type <span class="text-danger">*</span></label>
                        <select name="claim_type" class="form-select @error('claim_type') is-invalid @enderror" required>
                            @foreach(['teaching','module_development','consultation'] as $type)
                                <option value="{{ $type }}" {{ old('claim_type') === $type ? 'selected' : '' }}>{{ ucfirst(str_replace('_',' ',$type)) }}</option>
                            @endforeach
                        </select>
                        @error('claim_type') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Status <span class="text-danger">*</span></label>
                        <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                            @foreach(['draft','submitted','under_review','approved','returned','rejected'] as $status)
                                <option value="{{ $status }}" {{ old('status', 'submitted') === $status ? 'selected' : '' }}>{{ ucfirst(str_replace('_',' ',$status)) }}</option>
                            @endforeach
                        </select>
                        @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Total Hours <span class="text-danger">*</span></label>
                        <input type="number" name="total_hours" step="0.01" min="0" class="form-control @error('total_hours') is-invalid @enderror" value="{{ old('total_hours') }}" required>
                        @error('total_hours') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Rate per Hour (RM) <span class="text-danger">*</span></label>
                        <input type="number" name="rate_per_hour" step="0.01" min="0" class="form-control @error('rate_per_hour') is-invalid @enderror" value="{{ old('rate_per_hour') }}" required>
                        @error('rate_per_hour') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold">Executive Remarks</label>
                        <textarea name="executive_remarks" class="form-control @error('executive_remarks') is-invalid @enderror" rows="3">{{ old('executive_remarks') }}</textarea>
                        @error('executive_remarks') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold">Submission Checklist</label>
                        <div class="row g-2">
                            @foreach([
                                'has_mark_entry_forms' => 'Mark-entry Forms',
                                'has_qa' => 'Question Paper & Answer Sheet',
                            ] as $field => $label)
                                <div class="col-md-6">
                                    <div class="form-check border rounded px-3 py-2 h-100">
                                        <input type="checkbox" name="{{ $field }}" value="1"
                                            class="form-check-input ms-0 me-2" id="{{ $field }}"
                                            {{ old($field) ? 'checked' : '' }}>
                                        <label class="form-check-label ps-2" for="{{ $field }}">{{ $label }}</label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card">
            <div class="card-header bg-white fw-semibold">Create</div>
            <div class="card-body">
                <p class="text-muted small">The total amount will be calculated from hours x rate.</p>
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-plus-lg me-1"></i> Create Claim
                </button>
            </div>
        </div>
    </div>
</div>

</form>
</x-layouts.app>
