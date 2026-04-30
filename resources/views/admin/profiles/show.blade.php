<x-layouts.app title="Profile Detail">

<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="fw-bold mb-0">{{ $profile->full_name }}</h5>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.profiles.edit', $profile) }}" class="btn btn-primary btn-sm">
            <i class="bi bi-pencil me-1"></i> Edit
        </a>
        <form method="POST" action="{{ route('admin.profiles.destroy', $profile) }}" onsubmit="return confirm('Delete this profile? This will also remove all linked certificates and resume files. This cannot be undone.');">
            @csrf @method('DELETE')
            <button type="submit" class="btn btn-danger btn-sm">
                <i class="bi bi-trash me-1"></i> Delete
            </button>
        </form>
        <a href="{{ route('admin.profiles.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left"></i> Back
        </a>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card mb-4">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <span class="fw-semibold">Profile Information</span>
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
                    <dt class="col-sm-4 text-muted">Qualification</dt>
                    <dd class="col-sm-8">{{ $profile->qualification }} ({{ ucfirst($profile->qualification_level) }})</dd>
                    <dt class="col-sm-4 text-muted">Bank</dt>
                    <dd class="col-sm-8">{{ $profile->bank_name }} – {{ $profile->bank_account_number }}</dd>
                </dl>
            </div>
        </div>

        <!-- Appointments Summary -->
        <div class="card mb-4">
            <div class="card-header bg-white fw-semibold">Appointments ({{ $profile->appointments->count() }})</div>
            <div class="card-body p-0">
                @forelse($profile->appointments as $appt)
                    <div class="d-flex justify-content-between align-items-center px-3 py-2 border-bottom small">
                        <div>
                            <span class="fw-semibold">{{ $appt->course_code }}</span> – {{ $appt->course_name }}
                            <span class="badge {{ $appt->role_type === 'af' ? 'bg-primary' : 'bg-info' }} ms-1">{{ strtoupper($appt->role_type) }}</span>
                        </div>
                        <div class="text-muted">{{ $appt->semester }}</div>
                    </div>
                @empty
                    <div class="text-muted text-center py-3 small">No appointments.</div>
                @endforelse
            </div>
        </div>

        <!-- Claims Summary -->
        <div class="card">
            <div class="card-header bg-white fw-semibold">Claims ({{ $profile->claims->count() }})</div>
            <div class="card-body p-0">
                @forelse($profile->claims as $claim)
                    <div class="d-flex justify-content-between align-items-center px-3 py-2 border-bottom small">
                        <div>
                            <span class="fw-semibold">{{ $claim->claim_reference }}</span>
                            <span class="text-muted ms-2">RM {{ number_format($claim->total_amount, 2) }}</span>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <x-status-badge :status="$claim->status" />
                            <a href="{{ route('admin.claims.show', $claim) }}" class="btn btn-sm btn-outline-secondary">View</a>
                        </div>
                    </div>
                @empty
                    <div class="text-muted text-center py-3 small">No claims.</div>
                @endforelse
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card">
            <div class="card-header bg-white fw-semibold">Account Info</div>
            <div class="card-body small">
                <div><strong>Login Name:</strong> {{ $profile->user->name }}</div>
                <div><strong>Login Email:</strong> {{ $profile->user->email }}</div>
                <div><strong>Role:</strong> {{ strtoupper($profile->user->role) }}</div>
                <div><strong>Active:</strong> {{ $profile->user->is_active ? 'Yes' : 'No' }}</div>
                <hr>
                @if($profile->status === 'verified')
                    <div><strong>Verified By:</strong> {{ $profile->verifier?->name }}</div>
                    <div><strong>Verified At:</strong> {{ $profile->verified_at?->format('d M Y') }}</div>
                @elseif($profile->status === 'rejected')
                    <div class="text-danger"><strong>Rejected:</strong> {{ $profile->rejection_reason }}</div>
                @endif
            </div>
        </div>
    </div>
</div>

</x-layouts.app>
