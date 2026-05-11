<x-layouts.app title="Document Checklist">

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h5 class="fw-bold mb-1">Document Checklist</h5>
        <div class="text-muted small">Monitor uploaded Attendance, QB-AS, and MEF files by course appointment.</div>
    </div>
</div>

<div class="card mb-3">
    <div class="card-body py-2">
        <form method="GET" class="d-flex gap-2 flex-wrap">
            <select name="course" class="form-select form-select-sm" style="max-width:180px">
                <option value="">All Courses</option>
                @foreach($courses as $course)
                    <option value="{{ $course }}" {{ ($filters['course'] ?? '') === $course ? 'selected' : '' }}>{{ $course }}</option>
                @endforeach
            </select>
            <select name="semester" class="form-select form-select-sm" style="max-width:180px">
                <option value="">All Semesters</option>
                @foreach($semesters as $semester)
                    <option value="{{ $semester }}" {{ ($filters['semester'] ?? '') === $semester ? 'selected' : '' }}>{{ $semester }}</option>
                @endforeach
            </select>
            <button class="btn btn-sm btn-primary" type="submit"><i class="bi bi-search"></i> Filter</button>
            <a href="{{ route('pc.document-checklist.index') }}" class="btn btn-sm btn-outline-secondary">Reset</a>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Course</th>
                        <th>AF/AD Name</th>
                        <th>Role</th>
                        <th class="text-center">Attendance</th>
                        <th class="text-center">QB-AS</th>
                        <th class="text-center">MEF</th>
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
                            $submissions = $appointment->profile->submissions
                                ->where('course', $appointment->course_code)
                                ->whereNotNull('file_path');

                            $hasAttendance = $submissions->contains(fn ($submission) => $submission->submission_type === \App\Models\Submission::TYPE_ATTENDANCE_SHEET)
                                || $appointment->claims
                                ->flatMap(fn ($claim) => $claim->documents)
                                ->contains(fn ($document) => $document->document_type === 'attendance_sheet' && $document->is_uploaded);

                            $hasQbAs = $submissions->contains(fn ($submission) => in_array($submission->submission_type, $qbAsTypes, true));
                            $hasMef = $submissions->contains(fn ($submission) => $submission->submission_type === \App\Models\Submission::TYPE_MARK_ENTRY_FORMS);
                        @endphp
                        <tr>
                            <td>
                                <div class="fw-semibold">{{ $appointment->course_code }}</div>
                                <div class="text-muted small">{{ $appointment->semester }}</div>
                            </td>
                            <td class="fw-semibold">{{ $appointment->profile->full_name }}</td>
                            <td>
                                <span class="badge {{ $roleClass }}">
                                    {{ $roleLabels[$appointment->role_type] ?? strtoupper($appointment->role_type) }}
                                </span>
                            </td>
                            <td class="text-center">
                                @if($hasAttendance)
                                    <i class="bi bi-check-circle-fill text-success fs-5" title="Uploaded"></i>
                                @else
                                    <i class="bi bi-circle text-muted fs-5" title="Not uploaded"></i>
                                @endif
                            </td>
                            <td class="text-center">
                                @if($hasQbAs)
                                    <i class="bi bi-check-circle-fill text-success fs-5" title="Uploaded"></i>
                                @else
                                    <i class="bi bi-circle text-muted fs-5" title="Not uploaded"></i>
                                @endif
                            </td>
                            <td class="text-center">
                                @if($hasMef)
                                    <i class="bi bi-check-circle-fill text-success fs-5" title="Uploaded"></i>
                                @else
                                    <i class="bi bi-circle text-muted fs-5" title="Not uploaded"></i>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">No appointments found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-3">{{ $appointments->links() }}</div>
    </div>
</div>

</x-layouts.app>
