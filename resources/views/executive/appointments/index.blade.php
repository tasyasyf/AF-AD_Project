<x-layouts.app title="Appointments">

<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="fw-bold mb-0">Appointments</h5>
    <a href="{{ route('executive.appointments.create') }}" class="btn btn-primary btn-sm">
        <i class="bi bi-plus-circle me-1"></i> New Appointment
    </a>
</div>

<div class="card mb-3">
    <div class="card-body py-2">
        <form method="GET" class="d-flex gap-2 flex-wrap">
            <input type="text" name="search" class="form-control form-control-sm" style="max-width:220px"
                placeholder="Course code / name..." value="{{ request('search') }}">
            <select name="role_type" class="form-select form-select-sm" style="max-width:140px">
                <option value="">All Roles</option>
                <option value="af" {{ request('role_type') === 'af' ? 'selected' : '' }}>AF</option>
                <option value="ad" {{ request('role_type') === 'ad' ? 'selected' : '' }}>AD</option>
            </select>
            <button class="btn btn-sm btn-primary" type="submit"><i class="bi bi-search"></i> Filter</button>
            <a href="{{ route('executive.appointments.index') }}" class="btn btn-sm btn-outline-secondary">Reset</a>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>AF/AD</th>
                        <th>Course Code</th>
                        <th>Course Name</th>
                        <th>Role</th>
                        <th>Semester</th>
                        <th>Start Date</th>
                        <th>Status</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($appointments as $appt)
                    <tr>
                        <td class="small fw-semibold">{{ $appt->profile->full_name }}</td>
                        <td class="fw-semibold">{{ $appt->course_code }}</td>
                        <td class="small">{{ $appt->course_name }}</td>
                        <td><span class="badge {{ $appt->role_type === 'af' ? 'bg-primary' : 'bg-info' }}">{{ strtoupper($appt->role_type) }}</span></td>
                        <td class="small">{{ $appt->semester }}</td>
                        <td class="small text-muted">{{ $appt->start_date->format('d M Y') }}</td>
                        <td>
                            @if($appt->is_active) <span class="badge bg-success">Active</span>
                            @else <span class="badge bg-secondary">Inactive</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('executive.appointments.show', $appt) }}" class="btn btn-sm btn-outline-secondary">View</a>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="8" class="text-center text-muted py-4">No appointments found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-3">{{ $appointments->links() }}</div>
    </div>
</div>

</x-layouts.app>
