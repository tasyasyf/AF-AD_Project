<x-layouts.app title="AF/AD Profiles (View)">

<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
    <div>
        <h5 class="fw-bold mb-1">AF/AD Profiles</h5>
        <span class="badge bg-secondary"><i class="bi bi-eye"></i> Read-only view · granted by administrator</span>
    </div>
    <form method="GET" class="d-flex gap-2">
        <input type="text" name="search" value="{{ $search }}" class="form-control form-control-sm" style="max-width:220px" placeholder="Name / IC / email...">
        <button class="btn btn-sm btn-primary"><i class="bi bi-search"></i></button>
    </form>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Qualification</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($profiles as $profile)
                        <tr class="detail-trigger" data-detail="detail-{{ $profile->id }}" data-title="{{ $profile->full_name }}">
                            <td class="fw-semibold">{{ $profile->full_name }}</td>
                            <td class="small">{{ $profile->contact_email ?? $profile->user?->email }}</td>
                            <td class="small">{{ $profile->qualification_level }}</td>
                            <td><x-status-badge :status="$profile->status" /></td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="text-center text-muted py-4">No profiles found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="mt-3">{{ $profiles->links() }}</div>

{{-- Hidden detail blocks (rendered into the modal on row click) --}}
<div class="d-none">
    @foreach($profiles as $profile)
        <div id="detail-{{ $profile->id }}">
            <dl class="row mb-0">
                <dt class="col-sm-4 text-muted">Full Name</dt>
                <dd class="col-sm-8">{{ $profile->full_name }}</dd>
                <dt class="col-sm-4 text-muted">Email</dt>
                <dd class="col-sm-8">{{ $profile->contact_email ?? $profile->user?->email ?? '—' }}</dd>
                <dt class="col-sm-4 text-muted">IC Number</dt>
                <dd class="col-sm-8">{{ $profile->ic_number ?: '—' }}</dd>
                <dt class="col-sm-4 text-muted">Phone</dt>
                <dd class="col-sm-8">{{ $profile->phone ?: '—' }}</dd>
                <dt class="col-sm-4 text-muted">Qualification</dt>
                <dd class="col-sm-8">{{ $profile->qualification_level }} {{ $profile->qualification ? '· '.$profile->qualification : '' }}</dd>
                <dt class="col-sm-4 text-muted">Specialisation</dt>
                <dd class="col-sm-8">{{ $profile->specialisation ?: '—' }}</dd>
                <dt class="col-sm-4 text-muted">Bank</dt>
                <dd class="col-sm-8">{{ $profile->bank_name ?: '—' }} {{ $profile->bank_account_number ? '· '.$profile->bank_account_number : '' }}</dd>
                <dt class="col-sm-4 text-muted">Status</dt>
                <dd class="col-sm-8">{{ ucfirst($profile->status) }}</dd>
            </dl>
        </div>
    @endforeach
</div>

@include('access.partials.detail-modal')

</x-layouts.app>
