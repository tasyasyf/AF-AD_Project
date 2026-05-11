<x-layouts.app title="New Submission">

<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="fw-bold mb-0">New Submission</h5>
    <a href="{{ route('afad.submissions.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i> Back
    </a>
</div>

<form method="POST" action="{{ route('afad.submissions.store') }}" enctype="multipart/form-data">
@csrf

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card mb-4">
            <div class="card-header bg-white fw-semibold">Submission Details</div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label fw-semibold">Submission Type <span class="text-danger">*</span></label>
                        <select name="submission_type" id="submission-type" class="form-select @error('submission_type') is-invalid @enderror" required>
                            @foreach($submissionTypes as $value => $label)
                                <option value="{{ $value }}" {{ old('submission_type') === $value ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('submission_type') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Submission Date <span class="text-danger">*</span></label>
                        <input type="date" name="submission_date" class="form-control @error('submission_date') is-invalid @enderror"
                            value="{{ old('submission_date', now()->toDateString()) }}" max="{{ now()->toDateString() }}" required>
                        @error('submission_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Course Code <span class="text-danger">*</span></label>
                        <select name="course" id="submission-course-code" class="form-select @error('course') is-invalid @enderror" required>
                            <option value="">Select course code...</option>
                            @foreach(['CIT400', 'CRM300', 'CSC400'] as $course)
                                <option value="{{ $course }}" {{ old('course') === $course ? 'selected' : '' }}>{{ $course }}</option>
                            @endforeach
                        </select>
                        @error('course') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Course Name <span class="text-danger">*</span></label>
                        <select name="course_name" id="submission-course-name" class="form-select @error('course_name') is-invalid @enderror" required>
                            <option value="">Select course name...</option>
                            @foreach(['Industrial Training', 'Customer Relationship Management', 'Software Construction'] as $courseName)
                                <option value="{{ $courseName }}" {{ old('course_name') === $courseName ? 'selected' : '' }}>{{ $courseName }}</option>
                            @endforeach
                        </select>
                        @error('course_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Programme <span class="text-danger">*</span></label>
                        <select name="programme" id="submission-programme" class="form-select @error('programme') is-invalid @enderror" required>
                            <option value="">Select programme...</option>
                            @foreach(['BBA', 'BICT', 'BDCM'] as $programme)
                                <option value="{{ $programme }}" {{ old('programme') === $programme ? 'selected' : '' }}>{{ $programme }}</option>
                            @endforeach
                        </select>
                        @error('programme') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6 video-only d-none">
                        <label class="form-label fw-semibold">Tutorial <span class="text-danger">*</span></label>
                        <select name="tutorial_number" id="tutorial-number" class="form-select @error('tutorial_number') is-invalid @enderror">
                            <option value="">Select tutorial...</option>
                            @foreach([1, 2, 3, 4, 5] as $tutorial)
                                <option value="{{ $tutorial }}" {{ (string) old('tutorial_number') === (string) $tutorial ? 'selected' : '' }}>Tutorial {{ $tutorial }}</option>
                            @endforeach
                        </select>
                        @error('tutorial_number') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-12 video-only d-none">
                        <label class="form-label fw-semibold">Video Recording Link <span class="text-danger">*</span></label>
                        <input type="url" name="video_url" id="video-url" class="form-control @error('video_url') is-invalid @enderror"
                            value="{{ old('video_url') }}" placeholder="https://drive.google.com/...">
                        <input type="hidden" name="video_duration_seconds" id="video-duration-seconds" value="{{ old('video_duration_seconds', 0) }}">
                        <div class="form-text">Paste a Google Drive link or any accessible video URL.</div>
                        @error('video_url') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6" id="rate-field-wrap">
                        <label class="form-label fw-semibold" id="submission-rate-label">Rate (RM) <span class="text-danger">*</span></label>
                        <input type="number" name="rate_per_hour" id="submission-rate" class="form-control @error('rate_per_hour') is-invalid @enderror"
                            value="{{ old('rate_per_hour') }}" min="0" step="0.01">
                        @error('rate_per_hour') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-4 question-bank-only d-none">
                        <label class="form-label fw-semibold">Semester Intake <span class="text-danger">*</span></label>
                        <select name="semester_intake" id="semester-intake" class="form-select @error('semester_intake') is-invalid @enderror">
                            <option value="">Select intake...</option>
                            @foreach(['January', 'May', 'September'] as $intake)
                                <option value="{{ $intake }}" {{ old('semester_intake') === $intake ? 'selected' : '' }}>{{ $intake }}</option>
                            @endforeach
                        </select>
                        @error('semester_intake') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold">Submission Title <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control @error('title') is-invalid @enderror"
                            value="{{ old('title') }}" placeholder="e.g. Week 5 Lecture Recording – CSC1234" required>
                        @error('title') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold">Description</label>
                        <textarea name="description" class="form-control @error('description') is-invalid @enderror"
                            rows="3" placeholder="Optional notes about this submission...">{{ old('description') }}</textarea>
                        @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-12" id="file-upload-section">
                        <label class="form-label fw-semibold">File Upload <span class="text-danger">*</span></label>
                        <input type="file" name="file" id="submission-file" class="d-none" accept="video/mp4,video/quicktime,video/webm,video/x-matroska,video/x-msvideo,video/x-ms-wmv">
                        <div id="file-preview" class="d-none border rounded p-3 mb-2 bg-light d-flex align-items-center gap-3">
                            <i class="bi bi-file-earmark-text text-primary fs-4"></i>
                            <div class="flex-grow-1">
                                <div class="fw-semibold" id="file-name"></div>
                                <div class="text-muted small" id="file-size"></div>
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-danger" id="file-remove-btn">
                                <i class="bi bi-x-lg"></i>
                            </button>
                        </div>
                        <button type="button" class="btn btn-outline-primary" id="file-upload-btn">
                            <i class="bi bi-upload me-1"></i> Choose File
                        </button>
                        <span class="text-muted small ms-2" id="file-rules">PDF only — max 5MB</span>
                        @error('file') <div class="text-danger small mt-2">{{ $message }}</div> @enderror
                        <div class="form-text mt-2" id="submission-help">
                            <i class="bi bi-info-circle me-1"></i>
                            This document submission will auto-check the matching item in New Claim.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card">
            <div class="card-header bg-white fw-semibold">Submit</div>
            <div class="card-body">
                <p class="text-muted small">After submission, the School Executive will review your file. Document submissions will automatically tick the matching checklist item in your new claim form.</p>
                <button type="submit" id="submission-submit" class="btn btn-primary w-100">
                    <i class="bi bi-send-fill me-1"></i> Submit
                </button>
            </div>
        </div>
    </div>
</div>

</form>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const fileInput = document.getElementById('submission-file');
    const uploadBtn = document.getElementById('file-upload-btn');
    const preview = document.getElementById('file-preview');
    const fileName = document.getElementById('file-name');
    const fileSize = document.getElementById('file-size');
    const removeBtn = document.getElementById('file-remove-btn');
    const submissionType = document.getElementById('submission-type');
    const fileUploadSection = document.getElementById('file-upload-section');
    const videoOnlyFields = document.querySelectorAll('.video-only');
    const questionBankOnlyFields = document.querySelectorAll('.question-bank-only');
    const tutorialNumber = document.getElementById('tutorial-number');
    const videoUrl = document.getElementById('video-url');
    const semesterIntake = document.getElementById('semester-intake');
    const submissionCourseCode = document.getElementById('submission-course-code');
    const submissionCourseName = document.getElementById('submission-course-name');
    const videoDurationSeconds = document.getElementById('video-duration-seconds');
    const submissionRate = document.getElementById('submission-rate');
    const rateFieldWrap = document.getElementById('rate-field-wrap');
    const submissionRateLabel = document.getElementById('submission-rate-label');
    const submitBtn = document.getElementById('submission-submit');
    const fileRules = document.getElementById('file-rules');
    const submissionHelp = document.getElementById('submission-help');
    const codeToName = {
        CIT400: 'Industrial Training',
        CRM300: 'Customer Relationship Management',
        CSC400: 'Software Construction',
    };
    const nameToCode = Object.fromEntries(Object.entries(codeToName).map(([code, name]) => [name, code]));

    function resetDuration() {
        videoDurationSeconds.value = '0';
    }

    function iconForFile(file) {
        if (file.type.startsWith('video/')) {
            return 'bi-camera-video text-danger';
        }
        if (file.type.includes('pdf')) {
            return 'bi-file-earmark-pdf text-danger';
        }
        return 'bi-file-earmark-text text-primary';
    }

    function updateFileRules() {
        const isVideo = submissionType.value === 'video_recording';
        const isAttendance = submissionType.value === 'attendance_sheet';
        const isQuestionBank = submissionType.value === 'question_bank_answer_sheet';
        fileInput.accept = isAttendance ? '.pdf,.xls,.xlsx,.csv' : '.pdf';
        fileInput.required = !isVideo;
        fileUploadSection.classList.toggle('d-none', isVideo);
        fileRules.textContent = isAttendance ? 'PDF or Excel — max 5MB' : 'PDF only — max 5MB';
        submissionHelp.innerHTML = isAttendance
            ? '<i class="bi bi-info-circle me-1"></i>Attendance Sheet uploads do not need a rate.'
            : '<i class="bi bi-info-circle me-1"></i>This document submission will auto-check the matching item in New Claim.';
        videoOnlyFields.forEach(field => field.classList.toggle('d-none', !isVideo));
        questionBankOnlyFields.forEach(field => field.classList.toggle('d-none', !isQuestionBank));
        videoOnlyFields.forEach(field => field.querySelectorAll('input, select, textarea').forEach(input => input.disabled = !isVideo));
        questionBankOnlyFields.forEach(field => field.querySelectorAll('input, select, textarea').forEach(input => input.disabled = !isQuestionBank));
        tutorialNumber.toggleAttribute('required', isVideo);
        videoUrl.toggleAttribute('required', isVideo);
        rateFieldWrap.classList.toggle('d-none', isAttendance);
        submissionRate.disabled = isAttendance;
        submissionRate.toggleAttribute('required', !isAttendance);
        submissionRateLabel.innerHTML = isVideo
            ? 'Rate per Hour (RM) <span class="text-danger">*</span>'
            : 'Rate (RM) <span class="text-danger">*</span>';
        if (isAttendance) {
            submissionRate.value = '';
        }
        semesterIntake.toggleAttribute('required', isQuestionBank);
        if (!isVideo) {
            tutorialNumber.value = '';
            videoUrl.value = '';
            resetDuration();
            submitBtn.disabled = false;
        }
        if (!isQuestionBank) {
            semesterIntake.value = '';
        }
    }

    submissionCourseCode.addEventListener('change', () => {
        submissionCourseName.value = codeToName[submissionCourseCode.value] || '';
    });
    submissionCourseName.addEventListener('change', () => {
        submissionCourseCode.value = nameToCode[submissionCourseName.value] || '';
    });

    uploadBtn.addEventListener('click', () => fileInput.click());
    submissionType.addEventListener('change', function () {
        fileInput.value = '';
        preview.classList.add('d-none');
        uploadBtn.innerHTML = '<i class="bi bi-upload me-1"></i> Choose File';
        resetDuration();
        updateFileRules();
    });

    fileInput.addEventListener('change', function () {
        if (this.files.length) {
            const file = this.files[0];
            fileName.textContent = file.name;
            fileSize.textContent = file.size >= 1024 * 1024
                ? (file.size / 1024 / 1024).toFixed(2) + ' MB'
                : (file.size / 1024).toFixed(1) + ' KB';
            preview.querySelector('i').className = 'bi ' + iconForFile(file) + ' fs-4';
            preview.classList.remove('d-none');
            uploadBtn.innerHTML = '<i class="bi bi-arrow-repeat me-1"></i> Replace File';

            resetDuration();
            submitBtn.disabled = false;
        }
    });

    removeBtn.addEventListener('click', function () {
        fileInput.value = '';
        preview.classList.add('d-none');
        resetDuration();
        uploadBtn.innerHTML = '<i class="bi bi-upload me-1"></i> Choose File';
        submitBtn.disabled = false;
    });

    updateFileRules();
});
</script>
</x-layouts.app>
