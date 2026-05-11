<x-layouts.app title="Edit Appointment">

<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="fw-bold mb-0">Edit Appointment: {{ $appointment->course_code }}</h5>
    <a href="{{ route('executive.appointments.show', $appointment) }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i> Back
    </a>
</div>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-body p-4">
                <form method="POST" action="{{ route('executive.appointments.update', $appointment) }}">
                    @csrf @method('PUT')

                    <div class="mb-3">
                        <label class="form-label fw-semibold">AF/AD</label>
                        <input type="text" class="form-control" disabled value="{{ $appointment->profile->full_name }}">
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Course Code <span class="text-danger">*</span></label>
                            <select name="course_code" data-course-code class="form-select @error('course_code') is-invalid @enderror" required>
                                <option value="CIT400" {{ old('course_code', $appointment->course_code) === 'CIT400' ? 'selected' : '' }}>CIT400</option>
                                <option value="CRM300" {{ old('course_code', $appointment->course_code) === 'CRM300' ? 'selected' : '' }}>CRM300</option>
                                <option value="CSC400" {{ old('course_code', $appointment->course_code) === 'CSC400' ? 'selected' : '' }}>CSC400</option>
                            </select>
                            @error('course_code') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-8">
                            <label class="form-label fw-semibold">Course Name <span class="text-danger">*</span></label>
                            <select name="course_name" data-course-name class="form-select @error('course_name') is-invalid @enderror" required>
                                <option value="Industrial Training" {{ old('course_name', $appointment->course_name) === 'Industrial Training' ? 'selected' : '' }}>Industrial Training</option>
                                <option value="Customer Relationship Management" {{ old('course_name', $appointment->course_name) === 'Customer Relationship Management' ? 'selected' : '' }}>Customer Relationship Management</option>
                                <option value="Software Construction" {{ old('course_name', $appointment->course_name) === 'Software Construction' ? 'selected' : '' }}>Software Construction</option>
                            </select>
                            @error('course_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Role Type <span class="text-danger">*</span></label>
                            <select name="role_type" class="form-select" required>
                                <option value="af" {{ old('role_type', $appointment->role_type) === 'af' ? 'selected' : '' }}>AF</option>
                                <option value="ad" {{ old('role_type', $appointment->role_type) === 'ad' ? 'selected' : '' }}>AD</option>
                                <option value="af_internal" {{ old('role_type', $appointment->role_type) === 'af_internal' ? 'selected' : '' }}>AF Internal</option>
                                <option value="ad_internal" {{ old('role_type', $appointment->role_type) === 'ad_internal' ? 'selected' : '' }}>AD Internal</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Semester <span class="text-danger">*</span></label>
                            <input type="text" name="semester" class="form-control" value="{{ old('semester', $appointment->semester) }}" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Academic Session <span class="text-danger">*</span></label>
                            <input type="text" name="academic_session" class="form-control" value="{{ old('academic_session', $appointment->academic_session) }}" required>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Start Date <span class="text-danger">*</span></label>
                            <input type="date" name="start_date" class="form-control" value="{{ old('start_date', $appointment->start_date->format('Y-m-d')) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">End Date <span class="text-danger">*</span></label>
                            <input type="date" name="end_date" class="form-control" value="{{ old('end_date', $appointment->end_date->format('Y-m-d')) }}" required>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-8">
                            <label class="form-label fw-semibold">Venue</label>
                            <input type="text" name="venue" class="form-control" value="{{ old('venue', $appointment->venue) }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">No. of Students</label>
                            <input type="number" name="student_count" class="form-control" value="{{ old('student_count', $appointment->student_count) }}" min="1">
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="form-check">
                            <input type="hidden" name="is_active" value="0">
                            <input class="form-check-input" type="checkbox" name="is_active" value="1" id="is_active"
                                {{ old('is_active', $appointment->is_active) ? 'checked' : '' }}>
                            <label class="form-check-label fw-semibold" for="is_active">Active</label>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold">Notes</label>
                        <textarea name="notes" class="form-control" rows="2">{{ old('notes', $appointment->notes) }}</textarea>
                    </div>

                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save me-1"></i> Save Changes
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const codeToName = {
        CIT400: 'Industrial Training',
        CRM300: 'Customer Relationship Management',
        CSC400: 'Software Construction',
    };
    const nameToCode = Object.fromEntries(Object.entries(codeToName).map(([code, name]) => [name, code]));
    document.querySelectorAll('[data-course-code]').forEach(function (codeSelect) {
        const container = codeSelect.closest('form');
        const nameSelect = container.querySelector('[data-course-name]');
        codeSelect.addEventListener('change', () => nameSelect.value = codeToName[codeSelect.value] || '');
        nameSelect.addEventListener('change', () => codeSelect.value = nameToCode[nameSelect.value] || '');
    });
});
</script>

</x-layouts.app>
