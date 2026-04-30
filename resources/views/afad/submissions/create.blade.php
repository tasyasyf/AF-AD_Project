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
            <div class="card-header bg-white fw-semibold">Video Recording Submission</div>
            <div class="card-body">
                <div class="row g-3">
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
                    <div class="col-12">
                        <label class="form-label fw-semibold">File Upload <span class="text-danger">*</span></label>
                        <input type="file" name="file" id="submission-file" class="d-none" accept=".pdf,.xls,.xlsx,.csv">
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
                        <span class="text-muted small ms-2">PDF, Excel (XLS/XLSX) or CSV — max 5MB</span>
                        @error('file') <div class="text-danger small mt-2">{{ $message }}</div> @enderror
                        <div class="form-text mt-2">
                            <i class="bi bi-info-circle me-1"></i>
                            Upload an Excel or PDF file containing your video recording link(s).
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
                <p class="text-muted small">After submission, the School Executive will review your file by clicking on it from their dashboard.</p>
                <button type="submit" class="btn btn-primary w-100">
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

    uploadBtn.addEventListener('click', () => fileInput.click());

    fileInput.addEventListener('change', function () {
        if (this.files.length) {
            const file = this.files[0];
            fileName.textContent = file.name;
            fileSize.textContent = (file.size / 1024).toFixed(1) + ' KB';
            preview.classList.remove('d-none');
            uploadBtn.innerHTML = '<i class="bi bi-arrow-repeat me-1"></i> Replace File';
        }
    });

    removeBtn.addEventListener('click', function () {
        fileInput.value = '';
        preview.classList.add('d-none');
        uploadBtn.innerHTML = '<i class="bi bi-upload me-1"></i> Choose File';
    });
});
</script>
</x-layouts.app>
