<x-layouts.app title="Classes">

<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="fw-bold mb-0">All Classes</h5>
    <a href="{{ route('admin.classes.create') }}" class="btn btn-primary btn-sm">
        <i class="bi bi-plus-lg me-1"></i> New Class
    </a>
</div>

<div class="card mb-3">
    <div class="card-body py-2">
        <form method="GET" class="d-flex gap-2 flex-wrap">
            <input type="text" name="search" class="form-control form-control-sm" style="max-width:260px"
                placeholder="Search class / AFAD..." value="{{ request('search') }}">
            <button class="btn btn-sm btn-primary" type="submit"><i class="bi bi-search"></i> Filter</button>
            <a href="{{ route('admin.classes.index') }}" class="btn btn-sm btn-outline-secondary">Reset</a>
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
                        <th>Semester</th>
                        <th>Schedule</th>
                        <th>Students</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($classes as $class)
                    <tr>
                        <td class="fw-semibold small">{{ $class->profile->full_name }}</td>
                        <td><span class="fw-semibold">{{ $class->course_code }}</span><br><span class="small text-muted">{{ $class->course_name }}</span></td>
                        <td class="small">{{ $class->semester }}<br>{{ $class->academic_session }}</td>
                        <td class="small">{{ $class->day }} {{ \Carbon\Carbon::parse($class->start_time)->format('H:i') }}-{{ \Carbon\Carbon::parse($class->end_time)->format('H:i') }}</td>
                        <td>{{ $class->student_count ?? '—' }}</td>
                        <td>
                            <div class="d-flex gap-1">
                                <a href="{{ route('admin.classes.show', $class) }}" class="btn btn-sm btn-outline-secondary">View</a>
                                <a href="{{ route('admin.classes.edit', $class) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                                <form method="POST" action="{{ route('admin.classes.destroy', $class) }}" onsubmit="return confirm('Delete this class?');">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="text-center text-muted py-4">No classes found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-3">{{ $classes->links() }}</div>
    </div>
</div>

</x-layouts.app>
