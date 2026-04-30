<x-layouts.app title="Edit Appointment">

<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="fw-bold mb-0">Edit Appointment: {{ $appointment->course_code }}</h5>
    <a href="{{ route('admin.appointments.show', $appointment) }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i> Back
    </a>
</div>

<form method="POST" action="{{ route('admin.appointments.update', $appointment) }}">
@csrf @method('PUT')

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card mb-4">
            <div class="card-header bg-white fw-semibold">Course Information</div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Course Code <span class="text-danger">*</span></label>
                        <input type="text" name="course_code" class="form-control @error('course_code') is-invalid @enderror"
                            value="{{ old('course_code', $appointment->course_code) }}" required>
                        @error('course_code') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-8">
                        <label class="form-label fw-semibold">Course Name <span class="text-danger">*</span></label>
                        <input type="text" name="course_name" class="form-control @error('course_name') is-invalid @enderror"
                            value="{{ old('course_name', $appointment->course_name) }}" required>
                        @error('course_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Role Type <span class="text-danger">*</span></label>
                        <select name="role_type" class="form-select @error('role_type') is-invalid @enderror" required>
                            <option value="af" {{ old('role_type', $appointment->role_type) === 'af' ? 'selected' : '' }}>Academic Facilitator (AF)</option>
                            <option value="ad" {{ old('role_type', $appointment->role_type) === 'ad' ? 'selected' : '' }}>Academic Developer (AD)</option>
                        </select>
                        @error('role_type') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Semester <span class="text-danger">*</span></label>
                        <input type="text" name="semester" class="form-control @error('semester') is-invalid @enderror"
                            value="{{ old('semester', $appointment->semester) }}" required>
                        @error('semester') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Academic Session <span class="text-danger">*</span></label>
                        <input type="text" name="academic_session" class="form-control @error('academic_session') is-invalid @enderror"
                            value="{{ old('academic_session', $appointment->academic_session) }}" required>
                        @error('academic_session') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header bg-white fw-semibold">Schedule & Details</div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Start Date <span class="text-danger">*</span></label>
                        <input type="date" name="start_date" class="form-control @error('start_date') is-invalid @enderror"
                            value="{{ old('start_date', $appointment->start_date->format('Y-m-d')) }}" required>
                        @error('start_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">End Date <span class="text-danger">*</span></label>
                        <input type="date" name="end_date" class="form-control @error('end_date') is-invalid @enderror"
                            value="{{ old('end_date', $appointment->end_date->format('Y-m-d')) }}" required>
                        @error('end_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Venue</label>
                        <input type="text" name="venue" class="form-control @error('venue') is-invalid @enderror"
                            value="{{ old('venue', $appointment->venue) }}">
                        @error('venue') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Number of Students</label>
                        <input type="number" name="student_count" class="form-control @error('student_count') is-invalid @enderror"
                            value="{{ old('student_count', $appointment->student_count) }}" min="0">
                        @error('student_count') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold">Notes</label>
                        <textarea name="notes" class="form-control @error('notes') is-invalid @enderror" rows="2">{{ old('notes', $appointment->notes) }}</textarea>
                        @error('notes') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card mb-4">
            <div class="card-header bg-white fw-semibold">Status</div>
            <div class="card-body">
                <div class="form-check form-switch">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" name="is_active" value="1" class="form-check-input" id="is-active"
                        {{ old('is_active', $appointment->is_active) ? 'checked' : '' }}>
                    <label class="form-check-label fw-semibold" for="is-active">Active</label>
                </div>
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
