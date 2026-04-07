<x-layouts.app title="My Profile">

<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="fw-bold mb-0">My Profile</h5>
    @if(in_array($profile->status, ['pending', 'rejected']))
        <a href="{{ route('afad.profile.edit') }}" class="btn btn-outline-primary btn-sm">
            <i class="bi bi-pencil me-1"></i> Edit Profile
        </a>
    @endif
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card mb-4">
            <div class="card-header bg-white fw-semibold">Personal Information</div>
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
                </dl>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header bg-white fw-semibold">Qualification</div>
            <div class="card-body">
                <dl class="row mb-0">
                    <dt class="col-sm-4 text-muted">Qualification</dt>
                    <dd class="col-sm-8">{{ $profile->qualification }}</dd>
                    <dt class="col-sm-4 text-muted">Level</dt>
                    <dd class="col-sm-8">{{ ucfirst($profile->qualification_level) }}</dd>
                    <dt class="col-sm-4 text-muted">Specialisation</dt>
                    <dd class="col-sm-8">{{ $profile->specialisation ?? '—' }}</dd>
                </dl>
            </div>
        </div>

        <div class="card">
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
    </div>

    <div class="col-lg-4">
        <div class="card mb-4">
            <div class="card-header bg-white fw-semibold">Profile Status</div>
            <div class="card-body text-center py-4">
                <x-status-badge :status="$profile->status" />
                @if($profile->status === 'verified')
                    <div class="text-muted small mt-2">
                        Verified by {{ $profile->verifier?->name }}<br>
                        {{ $profile->verified_at?->format('d M Y') }}
                    </div>
                @elseif($profile->status === 'rejected')
                    <div class="alert alert-danger text-start mt-3 py-2 small">
                        <strong>Reason:</strong> {{ $profile->rejection_reason }}
                    </div>
                @endif
            </div>
        </div>

        <div class="card">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <span class="fw-semibold">Certificates</span>
                <a href="{{ route('afad.certificates.index') }}" class="btn btn-sm btn-outline-primary">Manage</a>
            </div>
            <div class="card-body">
                @forelse($profile->certificates as $cert)
                    <div class="d-flex align-items-center gap-2 py-1 border-bottom">
                        <i class="bi bi-award {{ $cert->is_verified ? 'text-success' : 'text-muted' }}"></i>
                        <div class="small">
                            <div class="fw-semibold">{{ $cert->title }}</div>
                            <div class="text-muted">{{ $cert->issuing_institution }}, {{ $cert->year_obtained }}</div>
                        </div>
                    </div>
                @empty
                    <p class="text-muted small mb-0">No certificates added.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>

</x-layouts.app>
