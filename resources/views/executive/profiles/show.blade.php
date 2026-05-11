<x-layouts.app title="Profile Review">

<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="fw-bold mb-0">Profile: {{ $profile->full_name }}</h5>
    <div class="d-flex gap-2">
        @if($profile->status !== 'verified')
            <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#verifyModal">
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

</x-layouts.app>
