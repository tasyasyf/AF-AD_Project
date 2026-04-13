<x-layouts.app title="Class Detail">

<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="fw-bold mb-0">Class Detail</h5>
    <div class="d-flex gap-2">
        <a href="{{ route('afad.classes.edit', $class) }}" class="btn btn-outline-primary btn-sm">
            <i class="bi bi-pencil me-1"></i> Edit
        </a>
        <a href="{{ route('afad.classes.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i> Back
        </a>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-7">
        <div class="card mb-4">
            <div class="card-header bg-white fw-semibold">Course Information</div>
            <div class="card-body">
                <dl class="row mb-0">
                    <dt class="col-sm-4 text-muted">Course Code</dt>
                    <dd class="col-sm-8 fw-semibold">{{ $class->course_code }}</dd>
                    <dt class="col-sm-4 text-muted">Course Name</dt>
                    <dd class="col-sm-8">{{ $class->course_name }}</dd>
                    <dt class="col-sm-4 text-muted">Section</dt>
                    <dd class="col-sm-8">{{ $class->section ?? '—' }}</dd>
                    <dt class="col-sm-4 text-muted">Semester</dt>
                    <dd class="col-sm-8">{{ $class->semester }}</dd>
                    <dt class="col-sm-4 text-muted">Academic Session</dt>
                    <dd class="col-sm-8">{{ $class->academic_session }}</dd>
                </dl>
            </div>
        </div>

        <div class="card">
            <div class="card-header bg-white fw-semibold">Schedule</div>
            <div class="card-body">
                <dl class="row mb-0">
                    <dt class="col-sm-4 text-muted">Day</dt>
                    <dd class="col-sm-8">{{ $class->day }}</dd>
                    <dt class="col-sm-4 text-muted">Time</dt>
                    <dd class="col-sm-8">{{ \Carbon\Carbon::parse($class->start_time)->format('h:i A') }} – {{ \Carbon\Carbon::parse($class->end_time)->format('h:i A') }}</dd>
                    <dt class="col-sm-4 text-muted">Venue</dt>
                    <dd class="col-sm-8">{{ $class->venue ?? '—' }}</dd>
                    <dt class="col-sm-4 text-muted">Students</dt>
                    <dd class="col-sm-8">{{ $class->student_count ?? '—' }}</dd>
                    <dt class="col-sm-4 text-muted">Notes</dt>
                    <dd class="col-sm-8">{{ $class->notes ?? '—' }}</dd>
                </dl>
            </div>
        </div>
    </div>

    <div class="col-lg-5">
        <div class="card">
            <div class="card-header bg-white fw-semibold">Actions</div>
            <div class="card-body">
                <a href="{{ route('afad.classes.edit', $class) }}" class="btn btn-outline-primary w-100 mb-2">
                    <i class="bi bi-pencil me-1"></i> Edit Class
                </a>
                <form method="POST" action="{{ route('afad.classes.destroy', $class) }}" onsubmit="return confirm('Are you sure you want to remove this class?');">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-outline-danger w-100">
                        <i class="bi bi-trash me-1"></i> Remove Class
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

</x-layouts.app>
