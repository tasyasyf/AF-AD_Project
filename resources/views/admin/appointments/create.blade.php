<x-layouts.app title="Create Appointment">

<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="fw-bold mb-0">Create Appointment</h5>
    <a href="{{ route('admin.appointments.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i> Back
    </a>
</div>

<form method="POST" action="{{ route('admin.appointments.store') }}">
@csrf

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card mb-4">
            <div class="card-header bg-white fw-semibold">Appointment Details</div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label fw-semibold">Assign To <span class="text-danger">*</span></label>
                        <select name="profile_id" class="form-select @error('profile_id') is-invalid @enderror" required>
                            <option value="">Select verified AF/AD...</option>
                            @foreach($profiles as $profile)
                                <option value="{{ $profile->id }}" {{ old('profile_id') == $profile->id ? 'selected' : '' }}>
                                    {{ $profile->full_name }} ({{ $profile->user->email }})
                                </option>
                            @endforeach
                        </select>
                        @error('profile_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
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
                        <label class="form-label fw-semibold">Role Type <span class="text-danger">*</span></label>
                        <select name="role_type" class="form-select @error('role_type') is-invalid @enderror" required>
                            <option value="">Select...</option>
                            <option value="af" {{ old('role_type') === 'af' ? 'selected' : '' }}>AF</option>
                            <option value="ad" {{ old('role_type') === 'ad' ? 'selected' : '' }}>AD</option>
                        </select>
                        @error('role_type') <div class="invalid-feedback">{{ $message }}</div> @enderror
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
                        <input type="text" name="academic_session" class="form-control @error('academic_session') is-invalid @enderror" value="{{ old('academic_session') }}" placeholder="e.g. 2026/2027" required>
                        @error('academic_session') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Start Date <span class="text-danger">*</span></label>
                        <input type="date" name="start_date" class="form-control @error('start_date') is-invalid @enderror" value="{{ old('start_date') }}" required>
                        @error('start_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">End Date <span class="text-danger">*</span></label>
                        <input type="date" name="end_date" class="form-control @error('end_date') is-invalid @enderror" value="{{ old('end_date') }}" required>
                        @error('end_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Venue</label>
                        <input type="text" name="venue" class="form-control @error('venue') is-invalid @enderror" value="{{ old('venue') }}">
                        @error('venue') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Number of Students</label>
                        <input type="number" name="student_count" class="form-control @error('student_count') is-invalid @enderror" value="{{ old('student_count') }}" min="0">
                        @error('student_count') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold">Notes</label>
                        <textarea name="notes" class="form-control @error('notes') is-invalid @enderror" rows="2">{{ old('notes') }}</textarea>
                        @error('notes') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card">
            <div class="card-header bg-white fw-semibold">Create</div>
            <div class="card-body">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-plus-lg me-1"></i> Create Appointment
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
    const codeSelect = document.querySelector('[data-course-code]');
    const nameSelect = document.querySelector('[data-course-name]');
    codeSelect.addEventListener('change', () => nameSelect.value = codeToName[codeSelect.value] || '');
    nameSelect.addEventListener('change', () => codeSelect.value = nameToCode[nameSelect.value] || '');
});
</script>
</x-layouts.app>
