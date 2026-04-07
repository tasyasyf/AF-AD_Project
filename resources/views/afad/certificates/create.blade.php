<x-layouts.app title="Add Certificate">

<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="fw-bold mb-0">Add Certificate</h5>
    <a href="{{ route('afad.certificates.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i> Back
    </a>
</div>

<div class="row justify-content-center">
    <div class="col-lg-7">
        <div class="card">
            <div class="card-body p-4">
                <form method="POST" action="{{ route('afad.certificates.store') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Certificate / Qualification Title <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control @error('title') is-invalid @enderror"
                            value="{{ old('title') }}" placeholder="e.g. Bachelor of Education (TESL)" required>
                        @error('title') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Issuing Institution <span class="text-danger">*</span></label>
                        <input type="text" name="issuing_institution" class="form-control @error('issuing_institution') is-invalid @enderror"
                            value="{{ old('issuing_institution') }}" placeholder="e.g. Universiti Malaya" required>
                        @error('issuing_institution') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Year Obtained <span class="text-danger">*</span></label>
                        <input type="number" name="year_obtained" class="form-control @error('year_obtained') is-invalid @enderror"
                            value="{{ old('year_obtained') }}" min="1950" max="{{ date('Y') }}" required>
                        @error('year_obtained') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="mb-4">
                        <label class="form-label fw-semibold">Upload Certificate (PDF/Image)</label>
                        <input type="file" name="certificate_file" class="form-control @error('certificate_file') is-invalid @enderror"
                            accept=".pdf,.jpg,.jpeg,.png">
                        <div class="form-text">Max 5 MB. PDF, JPG or PNG.</div>
                        @error('certificate_file') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-plus-circle me-1"></i> Add Certificate
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

</x-layouts.app>
