<x-layouts.app title="Appointments (View)">

<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
    <div>
        <h5 class="fw-bold mb-1">Appointments</h5>
        <span class="badge bg-secondary"><i class="bi bi-eye"></i> Read-only view · granted by administrator</span>
    </div>
    <form method="GET" class="d-flex gap-2">
        <input type="text" name="search" value="{{ $search }}" class="form-control form-control-sm" style="max-width:220px" placeholder="Course / AF/AD...">
        <button class="btn btn-sm btn-primary"><i class="bi bi-search"></i></button>
    </form>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Course</th>
                        <th>AF/AD</th>
                        <th>Semester</th>
                        <th>Period</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($appointments as $appointment)
                        <tr class="detail-trigger" data-detail="detail-{{ $appointment->id }}" data-title="{{ $appointment->course_code }} — {{ $appointment->course_name }}">
                            <td>
                                <div class="fw-semibold">{{ $appointment->course_code }}</div>
                                <div class="text-muted small">{{ $appointment->course_name }}</div>
                            </td>
                            <td class="small">{{ $appointment->profile?->full_name }}</td>
                            <td class="small">{{ $appointment->semester }}</td>
                            <td class="small">{{ $appointment->start_date?->format('d M Y') }} - {{ $appointment->end_date?->format('d M Y') }}</td>
                            <td>
                                <span class="badge {{ $appointment->is_active ? 'bg-success' : 'bg-secondary' }}">
                                    {{ $appointment->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center text-muted py-4">No appointments found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="mt-3">{{ $appointments->links() }}</div>

{{-- Hidden detail blocks (rendered into the modal on row click) --}}
<div class="d-none">
    @foreach($appointments as $appointment)
        <div id="detail-{{ $appointment->id }}">
            <dl class="row mb-0">
                <dt class="col-sm-4 text-muted">Course</dt>
                <dd class="col-sm-8">{{ $appointment->course_code }} — {{ $appointment->course_name }}</dd>
                <dt class="col-sm-4 text-muted">AF/AD</dt>
                <dd class="col-sm-8">{{ $appointment->profile?->full_name ?? '—' }}</dd>
                <dt class="col-sm-4 text-muted">Role Type</dt>
                <dd class="col-sm-8">{{ strtoupper(str_replace('_', ' ', $appointment->role_type ?? '—')) }}</dd>
                <dt class="col-sm-4 text-muted">Semester</dt>
                <dd class="col-sm-8">{{ $appointment->semester ?? '—' }}</dd>
                <dt class="col-sm-4 text-muted">Academic Session</dt>
                <dd class="col-sm-8">{{ $appointment->academic_session ?? '—' }}</dd>
                <dt class="col-sm-4 text-muted">Period</dt>
                <dd class="col-sm-8">{{ $appointment->start_date?->format('d M Y') }} – {{ $appointment->end_date?->format('d M Y') }}</dd>
                <dt class="col-sm-4 text-muted">Venue</dt>
                <dd class="col-sm-8">{{ $appointment->venue ?: '—' }}</dd>
                <dt class="col-sm-4 text-muted">Students</dt>
                <dd class="col-sm-8">{{ $appointment->student_count ?? '—' }}</dd>
                <dt class="col-sm-4 text-muted">Status</dt>
                <dd class="col-sm-8">{{ $appointment->is_active ? 'Active' : 'Inactive' }}</dd>
                @if($appointment->notes)
                    <dt class="col-sm-4 text-muted">Notes</dt>
                    <dd class="col-sm-8">{{ $appointment->notes }}</dd>
                @endif
            </dl>
        </div>
    @endforeach
</div>

@include('access.partials.detail-modal')

</x-layouts.app>
