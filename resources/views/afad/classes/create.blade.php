<x-layouts.app title="Add Class">

<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="fw-bold mb-0">Add Class</h5>
    <a href="{{ route('afad.classes.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i> Back
    </a>
</div>

<form method="POST" action="{{ route('afad.classes.store') }}">
@csrf

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card mb-4">
            <div class="card-header bg-white fw-semibold">Course Information</div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Course Code <span class="text-danger">*</span></label>
                        <select name="course_code" data-course-code class="form-select @error('course_code') is-invalid @enderror" required>
                            <option value="">Select course...</option>
                            <option value="CIT400" {{ old('course_code') === 'CIT400' ? 'selected' : '' }}>CIT400</option>
                            <option value="CRM300" {{ old('course_code') === 'CRM300' ? 'selected' : '' }}>CRM300</option>
                            <option value="CSC400" {{ old('course_code') === 'CSC400' ? 'selected' : '' }}>CSC400</option>
                        </select>
                        @error('course_code') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-8">
                        <label class="form-label fw-semibold">Course Name <span class="text-danger">*</span></label>
                        <select name="course_name" data-course-name class="form-select @error('course_name') is-invalid @enderror" required>
                            <option value="">Select course...</option>
                            <option value="Industrial Training" {{ old('course_name') === 'Industrial Training' ? 'selected' : '' }}>Industrial Training</option>
                            <option value="Customer Relationship Management" {{ old('course_name') === 'Customer Relationship Management' ? 'selected' : '' }}>Customer Relationship Management</option>
                            <option value="Software Construction" {{ old('course_name') === 'Software Construction' ? 'selected' : '' }}>Software Construction</option>
                        </select>
                        @error('course_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Section</label>
                        <input type="text" name="section" class="form-control @error('section') is-invalid @enderror"
                            value="{{ old('section') }}" placeholder="e.g. Section 01">
                        @error('section') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Semester <span class="text-danger">*</span></label>
                        <select name="semester" class="form-select @error('semester') is-invalid @enderror" required>
                            <option value="">Select semester...</option>
                            @foreach(['January', 'May', 'September'] as $semester)
                                <option value="{{ $semester }}" {{ old('semester') === $semester ? 'selected' : '' }}>{{ $semester }}</option>
                            @endforeach
                        </select>
                        @error('semester') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Academic Session <span class="text-danger">*</span></label>
                        <input type="text" name="academic_session" class="form-control @error('academic_session') is-invalid @enderror"
                            value="{{ old('academic_session') }}" placeholder="e.g. 2025/2026" required>
                        @error('academic_session') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header bg-white fw-semibold">Schedule</div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Day <span class="text-danger">*</span></label>
                        <select name="day" class="form-select @error('day') is-invalid @enderror" required>
                            <option value="">Select...</option>
                            @foreach(['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'] as $day)
                                <option value="{{ $day }}" {{ old('day') === $day ? 'selected' : '' }}>{{ $day }}</option>
                            @endforeach
                        </select>
                        @error('day') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Start Time <span class="text-danger">*</span></label>
                        <input type="time" name="start_time" class="form-control @error('start_time') is-invalid @enderror"
                            value="{{ old('start_time') }}" required>
                        @error('start_time') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">End Time <span class="text-danger">*</span></label>
                        <input type="time" name="end_time" class="form-control @error('end_time') is-invalid @enderror"
                            value="{{ old('end_time') }}" required>
                        @error('end_time') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Venue</label>
                        <input type="text" name="venue" class="form-control @error('venue') is-invalid @enderror"
                            value="{{ old('venue') }}" placeholder="e.g. Dewan Kuliah A">
                        @error('venue') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Number of Students</label>
                        <input type="number" name="student_count" class="form-control @error('student_count') is-invalid @enderror"
                            value="{{ old('student_count') }}" min="0">
                        @error('student_count') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold">Notes</label>
                        <textarea name="notes" class="form-control @error('notes') is-invalid @enderror"
                            rows="2" placeholder="Any additional notes...">{{ old('notes') }}</textarea>
                        @error('notes') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card">
            <div class="card-header bg-white fw-semibold">Submit</div>
            <div class="card-body">
                <p class="text-muted small">Add the class you will be conducting. You can edit or remove it later.</p>
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-plus-lg me-1"></i> Add Class
                </button>
            </div>
        </div>
    </div>
</div>

</form>
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
