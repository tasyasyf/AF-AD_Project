<x-layouts.app title="AF/AD Detail">

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h5 class="fw-bold mb-1">{{ $profile->full_name }}</h5>
        <div class="text-muted small">Verified AF/AD profile ready to be used for nomination.</div>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('pc.appointments.create', ['profile_id' => $profile->id]) }}" class="btn btn-primary btn-sm">
            <i class="bi bi-person-check me-1"></i> Assign Role
        </a>
        <a href="{{ route('pc.afad.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i> Back
        </a>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card mb-4">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <span class="fw-semibold">Profile Detail</span>
                <span class="badge bg-success">Verified</span>
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
                    <dt class="col-sm-4 text-muted">Qualification</dt>
                    <dd class="col-sm-8">{{ $profile->qualification }} ({{ ucfirst($profile->qualification_level) }})</dd>
                    <dt class="col-sm-4 text-muted">Specialisation</dt>
                    <dd class="col-sm-8">{{ $profile->specialisation ?? '—' }}</dd>
                    <dt class="col-sm-4 text-muted">Expertise</dt>
                    <dd class="col-sm-8">{{ $profile->area_of_expertise ?? '—' }}</dd>
                </dl>
            </div>
        </div>

        <div class="card">
            <div class="card-header bg-white fw-semibold">Appointments / Permissions</div>
            <div class="card-body p-0">
                @forelse($profile->appointments as $appointment)
                    @php
                        $roleLabels = [
                            'af' => 'AF',
                            'ad' => 'AD',
                            'af_internal' => 'AF Internal',
                            'ad_internal' => 'AD Internal',
                        ];
                    @endphp
                    <div class="d-flex justify-content-between align-items-center px-3 py-2 border-bottom">
                        <div>
                            <div class="fw-semibold small">{{ $appointment->course_code }} - {{ $appointment->course_name }}</div>
                            <div class="text-muted small">{{ $roleLabels[$appointment->role_type] ?? strtoupper($appointment->role_type) }} &bull; {{ $appointment->semester }} &bull; {{ $appointment->academic_session }}</div>
                        </div>
                        @if($appointment->is_active)
                            <span class="badge bg-success">Active</span>
                        @else
                            <span class="badge bg-secondary">Inactive</span>
                        @endif
                    </div>
                @empty
                    <div class="text-center text-muted py-4 small">No appointments yet. Profile is still available for nomination.</div>
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

        <div class="card mb-4">
            <div class="card-header bg-white fw-semibold">Verification</div>
            <div class="card-body small">
                <div><strong>Verified by:</strong> {{ $profile->verifier?->name ?? 'School Executive' }}</div>
                <div><strong>Verified at:</strong> {{ $profile->verified_at?->format('d M Y H:i') ?? '—' }}</div>
                <hr>
                <div class="text-success fw-semibold"><i class="bi bi-check-circle me-1"></i>Ready for nomination</div>
            </div>
        </div>

        <div class="card">
            <div class="card-header bg-white fw-semibold">Recent Claims</div>
            <div class="card-body p-0">
                @forelse($profile->claims as $claim)
                    <div class="px-3 py-2 border-bottom">
                        <div class="fw-semibold small">{{ $claim->claim_reference }}</div>
                        <div class="text-muted small">RM {{ number_format($claim->total_amount, 2) }} &bull; {{ ucfirst(str_replace('_', ' ', $claim->status)) }}</div>
                    </div>
                @empty
                    <div class="text-center text-muted py-3 small">No claims yet.</div>
                @endforelse
            </div>
        </div>
    </div>
</div>

</x-layouts.app>
