<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="fw-bold mb-0">{{ $isEdit ? 'Edit Submission' : 'Create Submission' }}</h5>
    <a href="{{ $isEdit ? route('admin.submissions.show', $submission) : route('admin.submissions.index') }}" class="btn btn-outline-secondary btn-sm">Back</a>
</div>

<form method="POST" action="{{ $action }}" enctype="multipart/form-data">
@csrf
@if($method) @method($method) @endif
<div class="row g-4">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header bg-white fw-semibold">Submission Details</div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label fw-semibold">AF/AD <span class="text-danger">*</span></label>
                        <select name="profile_id" class="form-select @error('profile_id') is-invalid @enderror" required>
                            <option value="">Select AF/AD...</option>
                            @foreach($profiles as $profile)
                                <option value="{{ $profile->id }}" {{ old('profile_id', $submission?->profile_id) == $profile->id ? 'selected' : '' }}>{{ $profile->full_name }}</option>
                            @endforeach
                        </select>
                        @error('profile_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Submission Type <span class="text-danger">*</span></label>
                        <select name="submission_type" id="submission-type" class="form-select @error('submission_type') is-invalid @enderror" required>
                            @foreach($submissionTypes as $value => $label)
                                <option value="{{ $value }}" {{ old('submission_type', $submission?->submission_type ?? 'video_recording') === $value ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('submission_type') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Submission Date <span class="text-danger">*</span></label>
                        <input type="date" name="submission_date" class="form-control @error('submission_date') is-invalid @enderror" value="{{ old('submission_date', $submission?->submission_date?->format('Y-m-d') ?? now()->toDateString()) }}" max="{{ now()->toDateString() }}" required>
                        @error('submission_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6 video-only d-none">
                        <label class="form-label fw-semibold">Tutorial <span class="text-danger">*</span></label>
                        <select name="tutorial_number" id="tutorial-number" class="form-select @error('tutorial_number') is-invalid @enderror">
                            <option value="">Select tutorial...</option>
                            @foreach([1,2,3,4,5] as $tutorial)
                                <option value="{{ $tutorial }}" {{ (string) old('tutorial_number', $submission?->tutorial_number) === (string) $tutorial ? 'selected' : '' }}>Tutorial {{ $tutorial }}</option>
                            @endforeach
                        </select>
                        @error('tutorial_number') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6 video-only d-none">
                        <label class="form-label fw-semibold">Duration Seconds <span class="text-danger">*</span></label>
                        <input type="number" name="video_duration_seconds" id="video-duration-seconds" class="form-control @error('video_duration_seconds') is-invalid @enderror" value="{{ old('video_duration_seconds', $submission?->video_duration_minutes ? (int) round($submission->video_duration_minutes * 60) : '') }}" min="0">
                        @error('video_duration_seconds') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-4 question-bank-only d-none">
                        <label class="form-label fw-semibold">Semester Intake <span class="text-danger">*</span></label>
                        <select name="semester_intake" id="semester-intake" class="form-select @error('semester_intake') is-invalid @enderror">
                            <option value="">Select intake...</option>
                            @foreach(['January','May','September'] as $intake)
                                <option value="{{ $intake }}" {{ old('semester_intake', $submission?->semester_intake) === $intake ? 'selected' : '' }}>{{ $intake }}</option>
                            @endforeach
                        </select>
                        @error('semester_intake') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-4 question-bank-only d-none">
                        <label class="form-label fw-semibold">Course <span class="text-danger">*</span></label>
                        <select name="course" id="question-bank-course" class="form-select @error('course') is-invalid @enderror">
                            <option value="">Select course...</option>
                            @foreach(['CRM300','CSC400','CIT400'] as $course)
                                <option value="{{ $course }}" {{ old('course', $submission?->course) === $course ? 'selected' : '' }}>{{ $course }}</option>
                            @endforeach
                        </select>
                        @error('course') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-4 question-bank-only d-none">
                        <label class="form-label fw-semibold">Programme <span class="text-danger">*</span></label>
                        <select name="programme" id="question-bank-programme" class="form-select @error('programme') is-invalid @enderror">
                            <option value="">Select programme...</option>
                            @foreach(['BBA','BICT','BDCM'] as $programme)
                                <option value="{{ $programme }}" {{ old('programme', $submission?->programme) === $programme ? 'selected' : '' }}>{{ $programme }}</option>
                            @endforeach
                        </select>
                        @error('programme') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-4 mark-entry-only d-none">
                        <label class="form-label fw-semibold">Course Code <span class="text-danger">*</span></label>
                        <select name="course" id="mark-entry-course" class="form-select @error('course') is-invalid @enderror">
                            <option value="">Select course code...</option>
                            @foreach(['CIT400','CRM300','CSC400'] as $course)
                                <option value="{{ $course }}" {{ old('course', $submission?->course) === $course ? 'selected' : '' }}>{{ $course }}</option>
                            @endforeach
                        </select>
                        @error('course') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-4 mark-entry-only d-none">
                        <label class="form-label fw-semibold">Course Name <span class="text-danger">*</span></label>
                        <select name="course_name" id="mark-entry-course-name" class="form-select @error('course_name') is-invalid @enderror">
                            <option value="">Select course name...</option>
                            @foreach(['Industrial Training','Customer Relationship Management','Software Construction'] as $courseName)
                                <option value="{{ $courseName }}" {{ old('course_name', $submission?->course_name) === $courseName ? 'selected' : '' }}>{{ $courseName }}</option>
                            @endforeach
                        </select>
                        @error('course_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-4 mark-entry-only d-none">
                        <label class="form-label fw-semibold">Programme <span class="text-danger">*</span></label>
                        <select name="programme" id="mark-entry-programme" class="form-select @error('programme') is-invalid @enderror">
                            <option value="">Select programme...</option>
                            @foreach(['BBA','BICT','BDCM'] as $programme)
                                <option value="{{ $programme }}" {{ old('programme', $submission?->programme) === $programme ? 'selected' : '' }}>{{ $programme }}</option>
                            @endforeach
                        </select>
                        @error('programme') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold">Title <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title', $submission?->title) }}" required>
                        @error('title') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold">Description</label>
                        <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="2">{{ old('description', $submission?->description) }}</textarea>
                        @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Status <span class="text-danger">*</span></label>
                        <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                            @foreach(['pending','reviewed'] as $status)
                                <option value="{{ $status }}" {{ old('status', $submission?->status ?? 'pending') === $status ? 'selected' : '' }}>{{ ucfirst($status) }}</option>
                            @endforeach
                        </select>
                        @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">File {{ $isEdit ? '' : '*' }}</label>
                        <input type="file" name="file" id="submission-file" class="form-control @error('file') is-invalid @enderror">
                        @if($submission?->file_original_name)<div class="form-text">Current: {{ $submission->file_original_name }}</div>@endif
                        @error('file') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold">Executive Remarks</label>
                        <textarea name="executive_remarks" class="form-control @error('executive_remarks') is-invalid @enderror" rows="2">{{ old('executive_remarks', $submission?->executive_remarks) }}</textarea>
                        @error('executive_remarks') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header bg-white fw-semibold">{{ $isEdit ? 'Save' : 'Create' }}</div>
            <div class="card-body">
                <button class="btn btn-primary w-100" type="submit">{{ $isEdit ? 'Save Changes' : 'Create Submission' }}</button>
            </div>
        </div>
    </div>
</div>
</form>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const type = document.getElementById('submission-type');
    const videoFields = document.querySelectorAll('.video-only');
    const qbankFields = document.querySelectorAll('.question-bank-only');
    const markFields = document.querySelectorAll('.mark-entry-only');
    const tutorial = document.getElementById('tutorial-number');
    const duration = document.getElementById('video-duration-seconds');
    const intake = document.getElementById('semester-intake');
    const course = document.getElementById('question-bank-course');
    const programme = document.getElementById('question-bank-programme');
    const markCourse = document.getElementById('mark-entry-course');
    const markCourseName = document.getElementById('mark-entry-course-name');
    const markProgramme = document.getElementById('mark-entry-programme');
    const codeToName = {CIT400: 'Industrial Training', CRM300: 'Customer Relationship Management', CSC400: 'Software Construction'};
    const nameToCode = Object.fromEntries(Object.entries(codeToName).map(([code, name]) => [name, code]));
    function updateFields() {
        const isVideo = type.value === 'video_recording';
        const isQbank = type.value === 'question_bank_answer_sheet';
        const isMark = type.value === 'mark_entry_forms';
        videoFields.forEach(field => field.classList.toggle('d-none', !isVideo));
        qbankFields.forEach(field => field.classList.toggle('d-none', !isQbank));
        markFields.forEach(field => field.classList.toggle('d-none', !isMark));
        videoFields.forEach(field => field.querySelectorAll('input, select, textarea').forEach(input => input.disabled = !isVideo));
        qbankFields.forEach(field => field.querySelectorAll('input, select, textarea').forEach(input => input.disabled = !isQbank));
        markFields.forEach(field => field.querySelectorAll('input, select, textarea').forEach(input => input.disabled = !isMark));
        tutorial.toggleAttribute('required', isVideo);
        duration.toggleAttribute('required', isVideo);
        intake.toggleAttribute('required', isQbank);
        course.toggleAttribute('required', isQbank);
        programme.toggleAttribute('required', isQbank);
        markCourse.toggleAttribute('required', isMark);
        markCourseName.toggleAttribute('required', isMark);
        markProgramme.toggleAttribute('required', isMark);
    }
    markCourse.addEventListener('change', () => markCourseName.value = codeToName[markCourse.value] || '');
    markCourseName.addEventListener('change', () => markCourse.value = nameToCode[markCourseName.value] || '');
    type.addEventListener('change', updateFields);
    updateFields();
});
</script>
