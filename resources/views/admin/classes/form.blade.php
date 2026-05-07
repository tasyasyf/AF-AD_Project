<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="fw-bold mb-0">{{ $isEdit ? 'Edit Class' : 'Create Class' }}</h5>
    <a href="{{ $isEdit ? route('admin.classes.show', $class) : route('admin.classes.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i> Back
    </a>
</div>

<form method="POST" action="{{ $action }}">
@csrf
@if($method) @method($method) @endif

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header bg-white fw-semibold">Class Details</div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label fw-semibold">AF/AD <span class="text-danger">*</span></label>
                        <select name="profile_id" class="form-select @error('profile_id') is-invalid @enderror" required>
                            <option value="">Select AF/AD...</option>
                            @foreach($profiles as $profile)
                                <option value="{{ $profile->id }}" {{ old('profile_id', $class?->profile_id) == $profile->id ? 'selected' : '' }}>{{ $profile->full_name }}</option>
                            @endforeach
                        </select>
                        @error('profile_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Course Code <span class="text-danger">*</span></label>
                        <select name="course_code" data-course-code class="form-select @error('course_code') is-invalid @enderror" required>
                            <option value="">Select course...</option>
                            @foreach(['CIT400','CRM300','CSC400'] as $code)
                                <option value="{{ $code }}" {{ old('course_code', $class?->course_code) === $code ? 'selected' : '' }}>{{ $code }}</option>
                            @endforeach
                        </select>
                        @error('course_code') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-8">
                        <label class="form-label fw-semibold">Course Name <span class="text-danger">*</span></label>
                        <select name="course_name" data-course-name class="form-select @error('course_name') is-invalid @enderror" required>
                            <option value="">Select course...</option>
                            @foreach(['Industrial Training','Customer Relationship Management','Software Construction'] as $name)
                                <option value="{{ $name }}" {{ old('course_name', $class?->course_name) === $name ? 'selected' : '' }}>{{ $name }}</option>
                            @endforeach
                        </select>
                        @error('course_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Section</label>
                        <input type="text" name="section" class="form-control @error('section') is-invalid @enderror" value="{{ old('section', $class?->section) }}">
                        @error('section') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Semester <span class="text-danger">*</span></label>
                        <select name="semester" class="form-select @error('semester') is-invalid @enderror" required>
                            <option value="">Select semester...</option>
                            @foreach(['January','May','September'] as $semester)
                                <option value="{{ $semester }}" {{ old('semester', $class?->semester) === $semester ? 'selected' : '' }}>{{ $semester }}</option>
                            @endforeach
                        </select>
                        @error('semester') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Academic Session <span class="text-danger">*</span></label>
                        <input type="text" name="academic_session" class="form-control @error('academic_session') is-invalid @enderror" value="{{ old('academic_session', $class?->academic_session) }}" required>
                        @error('academic_session') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Day <span class="text-danger">*</span></label>
                        <select name="day" class="form-select @error('day') is-invalid @enderror" required>
                            <option value="">Select day...</option>
                            @foreach(['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'] as $day)
                                <option value="{{ $day }}" {{ old('day', $class?->day) === $day ? 'selected' : '' }}>{{ $day }}</option>
                            @endforeach
                        </select>
                        @error('day') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Start Time <span class="text-danger">*</span></label>
                        <input type="time" name="start_time" class="form-control @error('start_time') is-invalid @enderror" value="{{ old('start_time', $class?->start_time ? \Carbon\Carbon::parse($class->start_time)->format('H:i') : '') }}" required>
                        @error('start_time') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">End Time <span class="text-danger">*</span></label>
                        <input type="time" name="end_time" class="form-control @error('end_time') is-invalid @enderror" value="{{ old('end_time', $class?->end_time ? \Carbon\Carbon::parse($class->end_time)->format('H:i') : '') }}" required>
                        @error('end_time') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Venue</label>
                        <input type="text" name="venue" class="form-control @error('venue') is-invalid @enderror" value="{{ old('venue', $class?->venue) }}">
                        @error('venue') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Number of Students</label>
                        <input type="number" name="student_count" min="0" class="form-control @error('student_count') is-invalid @enderror" value="{{ old('student_count', $class?->student_count) }}">
                        @error('student_count') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold">Notes</label>
                        <textarea name="notes" class="form-control @error('notes') is-invalid @enderror" rows="2">{{ old('notes', $class?->notes) }}</textarea>
                        @error('notes') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header bg-white fw-semibold">{{ $isEdit ? 'Save' : 'Create' }}</div>
            <div class="card-body">
                <button class="btn btn-primary w-100" type="submit">{{ $isEdit ? 'Save Changes' : 'Create Class' }}</button>
            </div>
        </div>
    </div>
</div>
</form>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const codeToName = {CIT400: 'Industrial Training', CRM300: 'Customer Relationship Management', CSC400: 'Software Construction'};
    const nameToCode = Object.fromEntries(Object.entries(codeToName).map(([code, name]) => [name, code]));
    const codeSelect = document.querySelector('[data-course-code]');
    const nameSelect = document.querySelector('[data-course-name]');
    codeSelect.addEventListener('change', () => nameSelect.value = codeToName[codeSelect.value] || '');
    nameSelect.addEventListener('change', () => codeSelect.value = nameToCode[nameSelect.value] || '');
});
</script>
