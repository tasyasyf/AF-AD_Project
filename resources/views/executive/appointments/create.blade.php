<x-layouts.app title="New Appointment">

<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="fw-bold mb-0">Create New Appointment</h5>
    <a href="{{ route('executive.appointments.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i> Back
    </a>
</div>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-body p-4">
                <form method="POST" action="{{ route('executive.appointments.store') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Assign To (AF/AD) <span class="text-danger">*</span></label>
                        <select name="profile_id" class="form-select @error('profile_id') is-invalid @enderror" required>
                            <option value="">Select verified AF/AD...</option>
                            @foreach($profiles as $p)
                                <option value="{{ $p->id }}" {{ old('profile_id') == $p->id ? 'selected' : '' }}>
                                    {{ $p->full_name }} ({{ $p->user->email }})
                                </option>
                            @endforeach
                        </select>
                        @error('profile_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Course Code <span class="text-danger">*</span></label>
                            <input type="text" name="course_code" class="form-control @error('course_code') is-invalid @enderror"
                                value="{{ old('course_code') }}" placeholder="e.g. BBB1013" required>
                            @error('course_code') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-8">
                            <label class="form-label fw-semibold">Course Name <span class="text-danger">*</span></label>
                            <input type="text" name="course_name" class="form-control @error('course_name') is-invalid @enderror"
                                value="{{ old('course_name') }}" required>
                            @error('course_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Role Type <span class="text-danger">*</span></label>
                            <select name="role_type" class="form-select @error('role_type') is-invalid @enderror" required>
                                <option value="">Select...</option>
                                <option value="af" {{ old('role_type') === 'af' ? 'selected' : '' }}>AF – Academic Facilitator</option>
                                <option value="ad" {{ old('role_type') === 'ad' ? 'selected' : '' }}>AD – Academic Developer</option>
                            </select>
                            @error('role_type') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Semester <span class="text-danger">*</span></label>
                            <input type="text" name="semester" class="form-control @error('semester') is-invalid @enderror"
                                value="{{ old('semester') }}" placeholder="e.g. 2024/2025-1" required>
                            @error('semester') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Academic Session <span class="text-danger">*</span></label>
                            <input type="text" name="academic_session" class="form-control @error('academic_session') is-invalid @enderror"
                                value="{{ old('academic_session') }}" placeholder="e.g. 2024/2025" required>
                            @error('academic_session') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Start Date <span class="text-danger">*</span></label>
                            <input type="date" name="start_date" class="form-control @error('start_date') is-invalid @enderror"
                                value="{{ old('start_date') }}" required>
                            @error('start_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">End Date <span class="text-danger">*</span></label>
                            <input type="date" name="end_date" class="form-control @error('end_date') is-invalid @enderror"
                                value="{{ old('end_date') }}" required>
                            @error('end_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-8">
                            <label class="form-label fw-semibold">Venue</label>
                            <input type="text" name="venue" class="form-control" value="{{ old('venue') }}" placeholder="Optional">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">No. of Students</label>
                            <input type="number" name="student_count" class="form-control" value="{{ old('student_count') }}" min="1">
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold">Notes</label>
                        <textarea name="notes" class="form-control" rows="2">{{ old('notes') }}</textarea>
                    </div>

                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-plus-circle me-1"></i> Create Appointment
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

</x-layouts.app>
