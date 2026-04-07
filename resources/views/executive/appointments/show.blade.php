<x-layouts.app title="Appointment Detail">

<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="fw-bold mb-0">Appointment: {{ $appointment->course_code }}</h5>
    <div class="d-flex gap-2">
        <a href="{{ route('executive.appointments.edit', $appointment) }}" class="btn btn-outline-primary btn-sm">
            <i class="bi bi-pencil me-1"></i> Edit
        </a>
        <a href="{{ route('executive.appointments.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left"></i> Back
        </a>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-7">
        <div class="card mb-4">
            <div class="card-header bg-white fw-semibold">Course Information</div>
            <div class="card-body">
                <dl class="row mb-0">
                    <dt class="col-sm-4 text-muted">AF/AD</dt>
                    <dd class="col-sm-8 fw-semibold">{{ $appointment->profile->full_name }}</dd>
                    <dt class="col-sm-4 text-muted">Course Code</dt>
                    <dd class="col-sm-8">{{ $appointment->course_code }}</dd>
                    <dt class="col-sm-4 text-muted">Course Name</dt>
                    <dd class="col-sm-8">{{ $appointment->course_name }}</dd>
                    <dt class="col-sm-4 text-muted">Role</dt>
                    <dd class="col-sm-8">
                        <span class="badge {{ $appointment->role_type === 'af' ? 'bg-primary' : 'bg-info' }}">
                            {{ $appointment->role_type === 'af' ? 'Academic Facilitator (AF)' : 'Academic Developer (AD)' }}
                        </span>
                    </dd>
                    <dt class="col-sm-4 text-muted">Semester</dt>
                    <dd class="col-sm-8">{{ $appointment->semester }}</dd>
                    <dt class="col-sm-4 text-muted">Session</dt>
                    <dd class="col-sm-8">{{ $appointment->academic_session }}</dd>
                    <dt class="col-sm-4 text-muted">Period</dt>
                    <dd class="col-sm-8">{{ $appointment->start_date->format('d M Y') }} – {{ $appointment->end_date->format('d M Y') }}</dd>
                    <dt class="col-sm-4 text-muted">Venue</dt>
                    <dd class="col-sm-8">{{ $appointment->venue ?? '—' }}</dd>
                    <dt class="col-sm-4 text-muted">Students</dt>
                    <dd class="col-sm-8">{{ $appointment->student_count ?? '—' }}</dd>
                    <dt class="col-sm-4 text-muted">Appointed By</dt>
                    <dd class="col-sm-8">{{ $appointment->appointedBy?->name ?? '—' }}</dd>
                </dl>
            </div>
        </div>
    </div>
    <div class="col-lg-5">
        <div class="card">
            <div class="card-header bg-white fw-semibold">Claims</div>
            <div class="card-body p-0">
                @forelse($appointment->claims as $claim)
                    <div class="d-flex justify-content-between align-items-center px-3 py-2 border-bottom">
                        <div>
                            <div class="fw-semibold small">{{ $claim->claim_reference }}</div>
                            <div class="text-muted small">RM {{ number_format($claim->total_amount, 2) }}</div>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <x-status-badge :status="$claim->status" />
                            <a href="{{ route('executive.claims.show', $claim) }}" class="btn btn-sm btn-outline-secondary">View</a>
                        </div>
                    </div>
                @empty
                    <div class="text-center text-muted py-4 small">No claims yet.</div>
                @endforelse
            </div>
        </div>
    </div>
</div>

</x-layouts.app>
