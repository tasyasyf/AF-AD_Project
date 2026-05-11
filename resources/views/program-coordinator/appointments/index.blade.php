<x-layouts.app title="PC Appointments">

<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="fw-bold mb-0">Appointments</h5>
    <a href="{{ route('pc.appointments.create') }}" class="btn btn-primary btn-sm">
        <i class="bi bi-person-check me-1"></i> Assign Role
    </a>
</div>

<div class="card mb-3">
    <div class="card-body py-2">
        <form method="GET" class="d-flex gap-2 flex-wrap">
            <input type="text" name="search" class="form-control form-control-sm" style="max-width:230px"
                placeholder="AF/AD or course..." value="{{ request('search') }}">
            <select name="semester" class="form-select form-select-sm" style="max-width:170px">
                <option value="">All Semesters</option>
                @foreach($semesters as $semester)
                    <option value="{{ $semester }}" {{ request('semester') === $semester ? 'selected' : '' }}>{{ $semester }}</option>
                @endforeach
            </select>
            <select name="course" class="form-select form-select-sm" style="max-width:150px">
                <option value="">All Courses</option>
                @foreach($courses as $course)
                    <option value="{{ $course }}" {{ request('course') === $course ? 'selected' : '' }}>{{ $course }}</option>
                @endforeach
            </select>
            <button class="btn btn-sm btn-primary" type="submit"><i class="bi bi-search"></i> Filter</button>
            <a href="{{ route('pc.appointments.index') }}" class="btn btn-sm btn-outline-secondary">Reset</a>
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
                        <th>Course</th>
                        <th>Role</th>
                        <th>Semester</th>
                        <th>Session</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($appointments as $appointment)
                        @php
                            $roleLabels = [
                                'af' => 'AF',
                                'ad' => 'AD',
                                'af_internal' => 'AF Internal',
                                'ad_internal' => 'AD Internal',
                            ];
                            $roleClass = str_contains($appointment->role_type, 'af') ? 'bg-primary' : 'bg-info';
                        @endphp
                        <tr>
                            <td class="fw-semibold">{{ $appointment->profile->full_name }}</td>
                            <td class="small">{{ $appointment->course_code }} - {{ $appointment->course_name }}</td>
                            <td><span class="badge {{ $roleClass }}">{{ $roleLabels[$appointment->role_type] ?? strtoupper($appointment->role_type) }}</span></td>
                            <td class="small">{{ $appointment->semester }}</td>
                            <td class="small">{{ $appointment->academic_session }}</td>
                            <td>
                                @if($appointment->is_active)
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-secondary">Inactive</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center text-muted py-4">No appointments found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-3">{{ $appointments->links() }}</div>
    </div>
</div>

</x-layouts.app>
