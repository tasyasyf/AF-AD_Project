<x-layouts.app title="My Appointments">

<h5 class="fw-bold mb-4">My Appointments</h5>

<div class="card">
    <div class="card-body p-0">
        @if($appointments->isEmpty())
            <div class="text-center text-muted py-5">
                <i class="bi bi-calendar3 fs-1 d-block mb-2"></i>
                No appointments assigned yet.
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Course Code</th>
                            <th>Course Name</th>
                            <th>Role</th>
                            <th>Semester</th>
                            <th>Period</th>
                            <th>Status</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($appointments as $appt)
                        @php
                            $roleLabels = [
                                'af' => 'AF',
                                'ad' => 'AD',
                                'af_internal' => 'AF Internal',
                                'ad_internal' => 'AD Internal',
                            ];
                            $roleClass = str_contains($appt->role_type, 'af') ? 'bg-primary' : 'bg-info';
                        @endphp
                        <tr>
                            <td class="fw-semibold">{{ $appt->course_code }}</td>
                            <td>{{ $appt->course_name }}</td>
                            <td>
                                <span class="badge {{ $roleClass }}">
                                    {{ $roleLabels[$appt->role_type] ?? strtoupper($appt->role_type) }}
                                </span>
                            </td>
                            <td>{{ $appt->semester }}</td>
                            <td class="small text-muted">
                                {{ $appt->start_date->format('d M Y') }} – {{ $appt->end_date->format('d M Y') }}
                            </td>
                            <td>
                                @if($appt->is_active)
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-secondary">Inactive</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('afad.appointments.show', $appt) }}" class="btn btn-sm btn-outline-secondary">
                                    View
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="p-3">{{ $appointments->links() }}</div>
        @endif
    </div>
</div>

</x-layouts.app>
