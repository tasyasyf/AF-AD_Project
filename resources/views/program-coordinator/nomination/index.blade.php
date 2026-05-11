<x-layouts.app title="Nomination">

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h5 class="fw-bold mb-1">Nomination</h5>
        <div class="text-muted small">Start from a verified AF/AD profile and use the profile detail for nomination readiness.</div>
    </div>
    <a href="{{ route('pc.appointments.create') }}" class="btn btn-primary btn-sm">
        <i class="bi bi-person-check me-1"></i> Assign Role
    </a>
</div>

<div class="row g-3">
    @forelse($profiles as $profile)
        <div class="col-md-6 col-xl-4">
            <a href="{{ route('pc.afad.show', $profile) }}" class="stat-card-link">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between gap-2">
                            <div>
                                <div class="fw-semibold">{{ $profile->full_name }}</div>
                                <div class="text-muted small">{{ $profile->contact_email }}</div>
                            </div>
                            <span class="badge bg-success align-self-start">Verified</span>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between align-items-center gap-2">
                            <div class="small text-muted">
                                {{ $profile->appointments->first()?->course_code ?? 'No active appointment yet' }}
                            </div>
                            <span class="btn btn-sm btn-outline-primary">Select</span>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    @empty
        <div class="col-12">
            <div class="card">
                <div class="card-body text-center text-muted py-4">No verified AF/AD profiles available for nomination.</div>
            </div>
        </div>
    @endforelse
</div>

<div class="mt-3">{{ $profiles->links() }}</div>

</x-layouts.app>
