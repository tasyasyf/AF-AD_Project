<x-layouts.app title="AF/AD List">

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h5 class="fw-bold mb-1">Verified AF/AD List</h5>
        <div class="text-muted small">Only profiles verified by School Executive are shown here.</div>
    </div>
</div>

<div class="card mb-3">
    <div class="card-body py-2">
        <form method="GET" class="d-flex gap-2 flex-wrap">
            <input type="text" name="search" class="form-control form-control-sm" style="max-width:230px"
                placeholder="Search name / email / IC..." value="{{ request('search') }}">
            <select name="semester" class="form-select form-select-sm" style="max-width:170px">
                <option value="">All Semesters</option>
                @foreach($semesters as $semester)
                    <option value="{{ $semester }}" {{ request('semester') === $semester ? 'selected' : '' }}>{{ $semester }}</option>
                @endforeach
            </select>
            <select name="course" class="form-select form-select-sm" style="max-width:250px">
                <option value="">All Subjects / Courses</option>
                @foreach($courses as $course)
                    <option value="{{ $course->course_code }}" {{ request('course') === $course->course_code ? 'selected' : '' }}>
                        {{ $course->course_code }} - {{ $course->course_name }}
                    </option>
                @endforeach
            </select>
            <button class="btn btn-sm btn-primary" type="submit"><i class="bi bi-search"></i> Filter</button>
            <a href="{{ route('pc.afad.index') }}" class="btn btn-sm btn-outline-secondary">Reset</a>
        </form>
    </div>
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
                        <th>Active Courses</th>
                        <th>Verified</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($profiles as $profile)
                        <tr>
                            <td class="fw-semibold">{{ $profile->full_name }}</td>
                            <td class="small">{{ $profile->contact_email }}</td>
                            <td class="small">{{ $profile->qualification_level }} - {{ Str::limit($profile->qualification, 36) }}</td>
                            <td class="small">
                                @forelse($profile->appointments->take(2) as $appointment)
                                    <span class="badge bg-light text-dark border">{{ $appointment->course_code }} / {{ $appointment->semester }}</span>
                                @empty
                                    <span class="text-muted">Ready for nomination</span>
                                @endforelse
                            </td>
                            <td class="small text-muted">{{ $profile->verified_at?->format('d M Y') ?? 'Verified' }}</td>
                            <td>
                                <a href="{{ route('pc.afad.show', $profile) }}" class="btn btn-sm btn-outline-primary">Select</a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center text-muted py-4">No verified AF/AD profiles found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-3">{{ $profiles->links() }}</div>
    </div>
</div>

</x-layouts.app>
