<x-layouts.app title="Class Detail">

<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="fw-bold mb-0">{{ $class->course_code }} - {{ $class->course_name }}</h5>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.classes.edit', $class) }}" class="btn btn-primary btn-sm"><i class="bi bi-pencil me-1"></i> Edit</a>
        <a href="{{ route('admin.classes.index') }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left me-1"></i> Back</a>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <dl class="row mb-0">
            <dt class="col-sm-3 text-muted">AF/AD</dt><dd class="col-sm-9">{{ $class->profile->full_name }}</dd>
            <dt class="col-sm-3 text-muted">Course</dt><dd class="col-sm-9">{{ $class->course_code }} - {{ $class->course_name }}</dd>
            <dt class="col-sm-3 text-muted">Section</dt><dd class="col-sm-9">{{ $class->section ?? '—' }}</dd>
            <dt class="col-sm-3 text-muted">Semester</dt><dd class="col-sm-9">{{ $class->semester }} / {{ $class->academic_session }}</dd>
            <dt class="col-sm-3 text-muted">Schedule</dt><dd class="col-sm-9">{{ $class->day }} {{ \Carbon\Carbon::parse($class->start_time)->format('H:i') }}-{{ \Carbon\Carbon::parse($class->end_time)->format('H:i') }}</dd>
            <dt class="col-sm-3 text-muted">Venue</dt><dd class="col-sm-9">{{ $class->venue ?? '—' }}</dd>
            <dt class="col-sm-3 text-muted">Students</dt><dd class="col-sm-9">{{ $class->student_count ?? '—' }}</dd>
            <dt class="col-sm-3 text-muted">Notes</dt><dd class="col-sm-9">{{ $class->notes ?? '—' }}</dd>
        </dl>
    </div>
</div>

</x-layouts.app>
