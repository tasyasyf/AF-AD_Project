<x-layouts.app title="My Classes">

<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="fw-bold mb-0">My Classes</h5>
    <a href="{{ route('afad.classes.create') }}" class="btn btn-primary btn-sm">
        <i class="bi bi-plus-lg me-1"></i> Add Class
    </a>
</div>

<div class="card">
    <div class="card-body p-0">
        @if($classes->isEmpty())
            <div class="text-center text-muted py-5">
                <i class="bi bi-journal-bookmark fs-1 d-block mb-2"></i>
                No classes added yet.
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Course Code</th>
                            <th>Course Name</th>
                            <th>Section</th>
                            <th>Day</th>
                            <th>Time</th>
                            <th>Venue</th>
                            <th>Semester</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($classes as $class)
                        <tr>
                            <td class="fw-semibold">{{ $class->course_code }}</td>
                            <td>{{ $class->course_name }}</td>
                            <td>{{ $class->section ?? '—' }}</td>
                            <td>{{ $class->day }}</td>
                            <td class="small text-muted">
                                {{ \Carbon\Carbon::parse($class->start_time)->format('h:i A') }} – {{ \Carbon\Carbon::parse($class->end_time)->format('h:i A') }}
                            </td>
                            <td>{{ $class->venue ?? '—' }}</td>
                            <td>{{ $class->semester }}</td>
                            <td>
                                <div class="d-flex gap-1">
                                    <a href="{{ route('afad.classes.show', $class) }}" class="btn btn-sm btn-outline-secondary">View</a>
                                    <a href="{{ route('afad.classes.edit', $class) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="p-3">{{ $classes->links() }}</div>
        @endif
    </div>
</div>

</x-layouts.app>
