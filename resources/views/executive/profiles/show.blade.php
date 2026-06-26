<x-layouts.app title="Profile Review">

@php
    $resumeComplete = $profile->resume_path && \Illuminate\Support\Facades\Storage::disk('local')->exists($profile->resume_path);
    $certificateFiles = $profile->certificates->filter(fn ($certificate) => filled($certificate->file_path));
    $certificatesComplete = $certificateFiles->isNotEmpty();
    $documentsComplete = $resumeComplete && $certificatesComplete;
@endphp

<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="fw-bold mb-0">Profile: {{ $profile->full_name }}</h5>
    <div class="d-flex gap-2">
        @if($profile->status !== 'verified')
            <button class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#documentCompletenessModal">
                <i class="bi bi-folder-check me-1"></i> Verify Documents
            </button>
            <button class="btn btn-success btn-sm {{ $profile->documents_verified_at ? '' : 'disabled' }}" data-bs-toggle="modal" data-bs-target="#verifyModal" @disabled(!$profile->documents_verified_at)>
                <i class="bi bi-check-circle me-1"></i> Verify Profile
            </button>
            <button class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#rejectModal">
                <i class="bi bi-x-circle me-1"></i> Reject
            </button>
        @endif
        <a href="{{ route('executive.profiles.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left"></i> Back
        </a>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card mb-4">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <span class="fw-semibold">Personal Information</span>
                <x-status-badge :status="$profile->status" />
            </div>
            <div class="card-body">
                <dl class="row mb-0">
                    <dt class="col-sm-4 text-muted">Full Name</dt>
                    <dd class="col-sm-8">{{ $profile->full_name }}</dd>
                    <dt class="col-sm-4 text-muted">IC Number</dt>
                    <dd class="col-sm-8">{{ $profile->ic_number }}</dd>
                    <dt class="col-sm-4 text-muted">Phone</dt>
                    <dd class="col-sm-8">{{ $profile->phone }}</dd>
                    <dt class="col-sm-4 text-muted">Email</dt>
                    <dd class="col-sm-8">{{ $profile->contact_email }}</dd>
                    <dt class="col-sm-4 text-muted">Address</dt>
                    <dd class="col-sm-8">{{ $profile->address }}</dd>
                    <dt class="col-sm-4 text-muted">Qualification</dt>
                    <dd class="col-sm-8">{{ $profile->qualification }} ({{ ucfirst($profile->qualification_level) }})</dd>
                    <dt class="col-sm-4 text-muted">Specialisation</dt>
                    <dd class="col-sm-8">{{ $profile->specialisation ?? '—' }}</dd>
                </dl>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header bg-white fw-semibold">Bank Information</div>
            <div class="card-body">
                <dl class="row mb-0">
                    <dt class="col-sm-4 text-muted">Bank</dt>
                    <dd class="col-sm-8">{{ $profile->bank_name }}</dd>
                    <dt class="col-sm-4 text-muted">Account Number</dt>
                    <dd class="col-sm-8">{{ $profile->bank_account_number }}</dd>
                    <dt class="col-sm-4 text-muted">Account Holder</dt>
                    <dd class="col-sm-8">{{ $profile->bank_account_holder }}</dd>
                </dl>
            </div>
        </div>

        <!-- Certificates -->
        <div class="card">
            <div class="card-header bg-white fw-semibold">Certificates</div>
            <div class="card-body p-0">
                @forelse($profile->certificates as $cert)
                    <div class="d-flex justify-content-between align-items-center px-3 py-2 border-bottom">
                        <div>
                            <div class="fw-semibold small">{{ $cert->title }}</div>
                            <div class="text-muted small">{{ $cert->issuing_institution }}, {{ $cert->year_obtained }}</div>
                            @if($cert->file_original_name)
                                <div class="text-success small"><i class="bi bi-file-earmark-check"></i> {{ $cert->file_original_name }}</div>
                            @endif
                        </div>
                        <span class="badge {{ $profile->status === 'verified' ? 'bg-success' : 'bg-warning text-dark' }}">
                            {{ $profile->status === 'verified' ? 'Verified with Profile' : 'Pending Profile Review' }}
                        </span>
                    </div>
                @empty
                    <div class="text-muted small text-center py-3">No certificates uploaded.</div>
                @endforelse
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card mb-4">
            <div class="card-header bg-white fw-semibold">Profile Photo</div>
            <div class="card-body text-center">
                @if($profile->user->profile_photo_path)
                    <img src="{{ route('profile-photo.show', $profile->user) }}" alt="{{ $profile->full_name }}" class="profile-photo-lg">
                @else
                    <span class="profile-photo-lg profile-photo-placeholder">
                        <i class="bi bi-person"></i>
                    </span>
                @endif
            </div>
        </div>

        @if($profile->status === 'verified')
            <div class="card border-success mb-4">
                <div class="card-body text-center py-4">
                    <i class="bi bi-check-circle-fill text-success fs-1"></i>
                    <div class="fw-semibold mt-2">Profile Verified</div>
                    <div class="text-muted small">by {{ $profile->verifier?->name }}<br>{{ $profile->verified_at?->format('d M Y H:i') }}</div>
                </div>
            </div>
        @elseif($profile->status === 'rejected')
            <div class="card border-danger mb-4">
                <div class="card-body">
                    <div class="fw-semibold text-danger mb-2"><i class="bi bi-x-circle-fill me-1"></i>Rejected</div>
                    <p class="small mb-0">{{ $profile->rejection_reason }}</p>
                    <div class="text-muted small mt-2">by {{ $profile->verifier?->name }}, {{ $profile->verified_at?->format('d M Y') }}</div>
                </div>
            </div>
        @endif

        <div class="card">
            <div class="card-header bg-white fw-semibold">Login Account</div>
            <div class="card-body small">
                <div><strong>Name:</strong> {{ $profile->user->name }}</div>
                <div><strong>Email:</strong> {{ $profile->user->email }}</div>
                <div><strong>Registered:</strong> {{ $profile->created_at->format('d M Y') }}</div>
            </div>
        </div>
    </div>
</div>

<!-- Reject Modal -->
@php
    $rejectionSectionOptions = [
        'personal' => 'Personal Information',
        'qualification' => 'Qualification',
        'bank' => 'Bank Information',
        'resume' => 'Resume / CV',
        'certificates' => 'Certificates',
        'other' => 'Other',
    ];
@endphp

<x-confirm-modal
    id="verifyModal"
    title="Verify Profile"
    message="Verify this profile?"
    :action="route('executive.profiles.verify', $profile)"
    confirmText="Verify Profile"
    confirmClass="btn-success"
/>

<x-confirm-modal
    id="rejectModal"
    title="Reject Profile"
    message="Please provide a reason for rejecting this profile. The AF/AD will be notified and can edit and resubmit."
    :action="route('executive.profiles.reject', $profile)"
    confirmText="Reject Profile"
    confirmClass="btn-danger"
    :fields="[
        ['name'=>'rejection_sections','label'=>'Section(s) to Edit','type'=>'checkboxes','required'=>true,'options'=>$rejectionSectionOptions],
        ['name'=>'rejection_reason','label'=>'Rejection Reason','required'=>true,'placeholder'=>'Explain exactly what the AF/AD needs to fix...'],
    ]"
/>

<div class="modal fade" id="documentCompletenessModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable se-document-modal">
        <div class="modal-content">
            <div class="modal-header se-document-modal-header">
                <div>
                    <h4 class="modal-title fw-bold mb-1">Verify Document Completeness</h4>
                    <div class="text-muted">SE confirms before verifying the AF/AD profile.</div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body se-document-modal-body">
                <div class="mb-4">
                    <div class="fw-bold fs-5">{{ $profile->full_name }}</div>
                    <div class="text-muted">{{ $profile->qualification_level }} &middot; {{ $profile->contact_email }}</div>
                </div>

                <div class="se-document-list mb-4">
                    <div class="se-document-row">
                        <div class="se-document-row-main">
                            <div class="fw-bold fs-5">Resume / CV</div>
                            @if($resumeComplete)
                                <div class="text-muted">{{ $profile->resume_original_name }}</div>
                                <div class="d-flex flex-wrap gap-2 mt-3">
                                    <button type="button" class="btn btn-outline-primary se-review-document-btn"
                                        data-review-url="{{ route('executive.profiles.resume.view', $profile) }}"
                                        data-review-title="Resume / CV">
                                        <i class="bi bi-eye me-1"></i> Review File
                                    </button>
                                    <a href="{{ route('executive.profiles.resume.view', $profile) }}" target="_blank" rel="noopener" class="btn btn-outline-secondary">
                                        <i class="bi bi-box-arrow-up-right me-1"></i> Open
                                    </a>
                                </div>
                            @else
                                <div class="text-muted">No resume uploaded.</div>
                            @endif
                        </div>
                        <span class="badge {{ $resumeComplete ? 'bg-success' : 'bg-secondary' }}">{{ $resumeComplete ? 'Uploaded' : 'Missing' }}</span>
                    </div>

                    <div class="se-document-row align-items-start">
                        <div class="se-document-row-main">
                            <div class="fw-bold fs-5 mb-3">Certificates</div>
                            @if($certificateFiles->isNotEmpty())
                                <div class="d-grid gap-2">
                                    @foreach($certificateFiles as $certificate)
                                        <div class="se-certificate-item">
                                            <div class="fw-semibold">{{ $certificate->title }}</div>
                                            <div class="text-muted">{{ $certificate->issuing_institution }} &middot; {{ $certificate->year_obtained }}</div>
                                            <div class="text-muted">{{ $certificate->file_original_name }}</div>
                                            <div class="d-flex flex-wrap gap-2 mt-3">
                                                <button type="button" class="btn btn-outline-primary se-review-document-btn"
                                                    data-review-url="{{ route('executive.profiles.certificates.view', [$profile, $certificate]) }}"
                                                    data-review-title="{{ $certificate->title }}">
                                                    <i class="bi bi-eye me-1"></i> Review File
                                                </button>
                                                <a href="{{ route('executive.profiles.certificates.view', [$profile, $certificate]) }}" target="_blank" rel="noopener" class="btn btn-outline-secondary">
                                                    <i class="bi bi-box-arrow-up-right me-1"></i> Open
                                                </a>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-muted">No certificate files uploaded.</div>
                            @endif
                        </div>
                        <span class="badge {{ $certificatesComplete ? 'bg-success' : 'bg-secondary' }}">{{ $certificatesComplete ? 'Uploaded' : 'Missing' }}</span>
                    </div>
                </div>

                <div class="se-document-preview-panel border rounded mb-3 d-none">
                    <div class="d-flex justify-content-between align-items-center gap-2 border-bottom px-3 py-2">
                        <div class="fw-semibold small se-document-preview-title">Document Preview</div>
                        <button type="button" class="btn btn-sm btn-outline-secondary se-document-preview-close">
                            <i class="bi bi-x-lg"></i>
                        </button>
                    </div>
                    <iframe class="se-document-preview-frame" title="Document preview"></iframe>
                </div>

                @if($profile->documents_verified_at)
                    <div class="alert alert-success mb-0">
                        Document completeness has already been confirmed.
                    </div>
                @elseif(!$documentsComplete)
                    <div class="alert alert-warning mb-0">
                        Complete Resume / CV and at least one certificate before confirming.
                    </div>
                @else
                    <form method="POST" action="{{ route('executive.profiles.documents.verify', $profile) }}">
                        @csrf
                        <button type="submit" class="btn btn-success btn-lg w-100">
                            <i class="bi bi-check2-circle me-1"></i> Confirm Document Completeness
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>
</div>

<style>
    .se-document-modal {
        max-width: min(96vw, 1400px);
    }
    .se-document-modal-header,
    .se-document-modal-body {
        padding: 1.75rem;
    }
    .se-document-list {
        border: 1px solid #dee2e6;
        border-radius: 8px;
        overflow: hidden;
    }
    .se-document-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 1rem;
        padding: 1.1rem 1.25rem;
        border-bottom: 1px solid #dee2e6;
    }
    .se-document-row:last-child {
        border-bottom: 0;
    }
    .se-document-row-main {
        flex: 1 1 auto;
        min-width: 0;
    }
    .se-certificate-item {
        border: 1px solid #dee2e6;
        border-radius: 8px;
        padding: 0.9rem 1rem;
    }
    .se-document-preview-frame {
        width: 100%;
        height: min(62vh, 520px);
        border: 0;
        background: #fff;
    }
</style>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const modal = document.getElementById('documentCompletenessModal');
    if (!modal) return;

    const previewPanel = modal.querySelector('.se-document-preview-panel');
    const previewFrame = modal.querySelector('.se-document-preview-frame');
    const previewTitle = modal.querySelector('.se-document-preview-title');
    const closeButton = modal.querySelector('.se-document-preview-close');

    modal.querySelectorAll('.se-review-document-btn').forEach(function (button) {
        button.addEventListener('click', function () {
            previewTitle.textContent = button.dataset.reviewTitle + ' Preview';
            previewFrame.src = button.dataset.reviewUrl;
            previewPanel.classList.remove('d-none');
            previewPanel.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        });
    });

    closeButton?.addEventListener('click', function () {
        previewPanel.classList.add('d-none');
        previewFrame.src = '';
    });

    modal.addEventListener('hidden.bs.modal', function () {
        previewPanel.classList.add('d-none');
        previewFrame.src = '';
    });
});
</script>

</x-layouts.app>
