<x-layouts.app title="Register Profile">

<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="fw-bold mb-0">Register Your Profile</h5>
</div>

<form method="POST" action="{{ route('afad.profile.store') }}" enctype="multipart/form-data">
@csrf

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card mb-4">
            <div class="card-header bg-white fw-semibold">Personal Information</div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Full Name <span class="text-danger">*</span></label>
                        <input type="text" name="full_name" class="form-control @error('full_name') is-invalid @enderror"
                            value="{{ old('full_name') }}" required>
                        @error('full_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">IC Number <span class="text-danger">*</span></label>
                        <input type="text" name="ic_number" class="form-control @error('ic_number') is-invalid @enderror"
                            value="{{ old('ic_number') }}" placeholder="e.g. 850101-01-1234" required>
                        @error('ic_number') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Date of Birth <span class="text-danger">*</span></label>
                        <input type="date" name="date_of_birth" class="form-control @error('date_of_birth') is-invalid @enderror"
                            value="{{ old('date_of_birth') }}" required>
                        @error('date_of_birth') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Gender <span class="text-danger">*</span></label>
                        <select name="gender" class="form-select @error('gender') is-invalid @enderror" required>
                            <option value="">Select...</option>
                            <option value="male" {{ old('gender') === 'male' ? 'selected' : '' }}>Male</option>
                            <option value="female" {{ old('gender') === 'female' ? 'selected' : '' }}>Female</option>
                        </select>
                        @error('gender') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Phone <span class="text-danger">*</span></label>
                        <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror"
                            value="{{ old('phone') }}" placeholder="e.g. 012-3456789" required>
                        @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Contact Email <span class="text-danger">*</span></label>
                        <input type="email" name="contact_email" class="form-control @error('contact_email') is-invalid @enderror"
                            value="{{ old('contact_email') }}" required>
                        @error('contact_email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold">Address <span class="text-danger">*</span></label>
                        <textarea name="address" class="form-control @error('address') is-invalid @enderror"
                            rows="3" required>{{ old('address') }}</textarea>
                        @error('address') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header bg-white fw-semibold">Qualification</div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-8">
                        <label class="form-label fw-semibold">Qualification <span class="text-danger">*</span></label>
                        <input type="text" name="qualification" class="form-control @error('qualification') is-invalid @enderror"
                            value="{{ old('qualification') }}" placeholder="e.g. Bachelor of Education (TESL)" required>
                        @error('qualification') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Level <span class="text-danger">*</span></label>
                        <select name="qualification_level" class="form-select @error('qualification_level') is-invalid @enderror" required>
                            <option value="">Select...</option>
                            @foreach(['diploma','degree','masters','phd','professional'] as $level)
                                <option value="{{ $level }}" {{ old('qualification_level') === $level ? 'selected' : '' }}>
                                    {{ ucfirst($level) }}
                                </option>
                            @endforeach
                        </select>
                        @error('qualification_level') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Specialisation</label>
                        <input type="text" name="specialisation" class="form-control @error('specialisation') is-invalid @enderror"
                            value="{{ old('specialisation') }}" placeholder="Optional">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Area of Expertise</label>
                        <input type="text" name="area_of_expertise" class="form-control @error('area_of_expertise') is-invalid @enderror"
                            value="{{ old('area_of_expertise') }}" placeholder="e.g. Web Development, Data Science">
                    </div>
                </div>
            </div>
        </div>

        {{-- Certification Uploads --}}
        <div class="card mb-4">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <span class="fw-semibold">Certifications / Verification Documents</span>
                <button type="button" class="btn btn-sm btn-outline-primary" id="add-certificate-btn">
                    <i class="bi bi-plus-lg me-1"></i> Add Certificate
                </button>
            </div>
            <div class="card-body">
                <p class="text-muted small mb-3">Upload your professional certifications and qualifications as supporting documents. Accepted formats: PDF, JPG, PNG (max 5MB each).</p>

                <div id="certificates-container">
                    {{-- Certificate rows will be added here by JS --}}
                </div>

                <div id="no-certificates-message" class="text-center text-muted py-3">
                    <i class="bi bi-file-earmark-plus" style="font-size: 2rem;"></i>
                    <p class="mt-2 mb-0">No certificates added yet. Click "Add Certificate" to upload.</p>
                </div>
            </div>
        </div>

        {{-- Resume / CV Upload --}}
        <div class="card mb-4">
            <div class="card-header bg-white fw-semibold">Resume / CV</div>
            <div class="card-body">
                <input type="file" name="resume" id="resume-input" class="d-none" accept=".pdf">
                <div id="resume-preview" class="d-none border rounded p-3 mb-3 bg-light d-flex align-items-center gap-3">
                    <i class="bi bi-file-earmark-pdf text-danger fs-4"></i>
                    <div class="flex-grow-1">
                        <div class="fw-semibold" id="resume-filename"></div>
                        <div class="text-muted small" id="resume-filesize"></div>
                    </div>
                    <button type="button" class="btn btn-sm btn-outline-danger" id="resume-remove-btn" title="Remove">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>
                <button type="button" class="btn btn-outline-primary" id="resume-upload-btn">
                    <i class="bi bi-upload me-1"></i> Upload Resume
                </button>
                <span class="text-muted small ms-2">PDF only — max 5MB</span>
                @error('resume') <div class="text-danger small mt-2">{{ $message }}</div> @enderror
            </div>
        </div>

        <div class="card">
            <div class="card-header bg-white fw-semibold">Bank Account Information</div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Bank Name <span class="text-danger">*</span></label>
                        <input type="text" name="bank_name" class="form-control @error('bank_name') is-invalid @enderror"
                            value="{{ old('bank_name') }}" placeholder="e.g. Maybank" required>
                        @error('bank_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Account Number <span class="text-danger">*</span></label>
                        <input type="text" name="bank_account_number" class="form-control @error('bank_account_number') is-invalid @enderror"
                            value="{{ old('bank_account_number') }}" required>
                        @error('bank_account_number') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold">Account Holder Name <span class="text-danger">*</span></label>
                        <input type="text" name="bank_account_holder" class="form-control @error('bank_account_holder') is-invalid @enderror"
                            value="{{ old('bank_account_holder') }}" required>
                        @error('bank_account_holder') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card mb-4">
            <div class="card-header bg-white fw-semibold">Profile Photo</div>
            <div class="card-body text-center">
                <x-profile-photo-uploader :user="auth()->user()" :photo-alt="auth()->user()->name" />
            </div>
        </div>

        <div class="card">
            <div class="card-header bg-white fw-semibold">Submit Registration</div>
            <div class="card-body">
                <p class="text-muted small">After registration, your profile will be reviewed and verified by the School Executive before you can submit claims.</p>
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-send-fill me-1"></i> Submit Profile
                </button>
            </div>
        </div>
    </div>
</div>

</form>

<script>
document.addEventListener('DOMContentLoaded', function () {
    let certIndex = 0;
    const container = document.getElementById('certificates-container');
    const noMessage = document.getElementById('no-certificates-message');
    const addBtn = document.getElementById('add-certificate-btn');

    function updateNoMessage() {
        noMessage.style.display = container.children.length === 0 ? 'block' : 'none';
    }

    addBtn.addEventListener('click', function () {
        const row = document.createElement('div');
        row.className = 'border rounded p-3 mb-3 position-relative';
        row.innerHTML = `
            <button type="button" class="btn btn-sm btn-outline-danger position-absolute top-0 end-0 m-2 remove-cert-btn" title="Remove">
                <i class="bi bi-x-lg"></i>
            </button>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Certificate Title <span class="text-danger">*</span></label>
                    <input type="text" name="certificates[${certIndex}][title]" class="form-control" placeholder="e.g. AWS Certified Developer" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Issuing Institution <span class="text-danger">*</span></label>
                    <input type="text" name="certificates[${certIndex}][issuing_institution]" class="form-control" placeholder="e.g. Amazon Web Services" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Year Obtained <span class="text-danger">*</span></label>
                    <input type="number" name="certificates[${certIndex}][year_obtained]" class="form-control" min="1950" max="${new Date().getFullYear()}" required>
                </div>
                <div class="col-md-8">
                    <label class="form-label fw-semibold">Upload File <span class="text-danger">*</span></label>
                    <input type="file" name="certificates[${certIndex}][file]" class="form-control" accept=".pdf,.jpg,.jpeg,.png" required>
                    <div class="form-text">PDF, JPG, or PNG — max 5MB</div>
                </div>
            </div>
        `;
        container.appendChild(row);
        certIndex++;
        updateNoMessage();

        row.querySelector('.remove-cert-btn').addEventListener('click', function () {
            row.remove();
            updateNoMessage();
        });
    });

    updateNoMessage();

    // Resume upload button
    const resumeInput = document.getElementById('resume-input');
    const resumeUploadBtn = document.getElementById('resume-upload-btn');
    const resumePreview = document.getElementById('resume-preview');
    const resumeFilename = document.getElementById('resume-filename');
    const resumeFilesize = document.getElementById('resume-filesize');
    const resumeRemoveBtn = document.getElementById('resume-remove-btn');

    resumeUploadBtn.addEventListener('click', () => resumeInput.click());

    resumeInput.addEventListener('change', function () {
        if (this.files.length) {
            const file = this.files[0];
            resumeFilename.textContent = file.name;
            resumeFilesize.textContent = (file.size / 1024).toFixed(1) + ' KB';
            resumePreview.classList.remove('d-none');
            resumeUploadBtn.innerHTML = '<i class="bi bi-arrow-repeat me-1"></i> Replace Resume';
        }
    });

    resumeRemoveBtn.addEventListener('click', function () {
        resumeInput.value = '';
        resumePreview.classList.add('d-none');
        resumeUploadBtn.innerHTML = '<i class="bi bi-upload me-1"></i> Upload Resume';
    });
});
</script>
</x-layouts.app>
