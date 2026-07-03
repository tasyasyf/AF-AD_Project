<x-layouts.app title="Reports">

<style>
    .pc-report-stat .card-body {
        min-height: 72px;
        padding: 0.7rem 0.85rem;
    }

    .pc-report-stat .rounded-circle {
        width: 38px !important;
        height: 38px !important;
        flex-basis: 38px;
    }

    .pc-report-stat .rounded-circle i {
        font-size: 1rem !important;
    }

    .pc-report-stat .text-muted.small {
        margin-bottom: 0.15rem;
        font-size: 0.72rem;
    }

    .pc-report-stat .fw-bold.fs-4 {
        font-size: 1.25rem !important;
    }
</style>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h5 class="fw-bold mb-1">Reports</h5>
        <div class="text-muted small">Monitor AF/AD appointments and claim activity by period, semester, and course.</div>
    </div>
    <a href="{{ route('pc.reports.export', request()->query()) }}" class="btn btn-sm btn-outline-primary">
        <i class="bi bi-download"></i> Export CSV
    </a>
</div>

<div class="card mb-3">
    <div class="card-body py-2">
        <form method="GET" class="d-flex gap-2 flex-wrap align-items-center">
            <select name="semester" class="form-select form-select-sm" style="max-width:170px">
                <option value="">All Semesters</option>
                @foreach($semesters as $semester)
                    <option value="{{ $semester }}" {{ ($filters['semester'] ?? '') === $semester ? 'selected' : '' }}>{{ $semester }}</option>
                @endforeach
            </select>
            <select name="course" class="form-select form-select-sm" style="max-width:240px">
                <option value="">All Courses</option>
                @foreach($courses as $course)
                    <option value="{{ $course->course_code }}" {{ ($filters['course'] ?? '') === $course->course_code ? 'selected' : '' }}>
                        {{ $course->course_code }} - {{ $course->course_name }}
                    </option>
                @endforeach
            </select>
            <select name="status" class="form-select form-select-sm" style="max-width:170px">
                <option value="">All Claim Statuses</option>
                @foreach(['draft','submitted','under_review','approved','returned','rejected'] as $status)
                    <option value="{{ $status }}" {{ ($filters['status'] ?? '') === $status ? 'selected' : '' }}>{{ ucfirst(str_replace('_', ' ', $status)) }}</option>
                @endforeach
            </select>
            <input type="text" name="search" class="form-control form-control-sm" style="max-width:220px"
                placeholder="Reference / AF/AD / course..." value="{{ $filters['search'] ?? '' }}">
            <button class="btn btn-sm btn-primary" type="submit"><i class="bi bi-search"></i> Filter</button>
            <a href="{{ route('pc.reports.index') }}" class="btn btn-sm btn-outline-secondary">Reset</a>
        </form>
    </div>
</div>

@php
    // Carry the active report scope (semester/course/search) into the linked lists.
    $listFilters = array_filter([
        'semester' => $filters['semester'] ?? null,
        'course' => $filters['course'] ?? null,
        'search' => $filters['search'] ?? null,
    ], fn ($value) => filled($value));
    $claimFilters = array_filter([
        'search' => $filters['search'] ?? null,
    ], fn ($value) => filled($value));
@endphp

<div class="row g-3 mb-3">
    @foreach([
        ['label' => 'Verified AF/AD', 'value' => $summary['verified_profiles'], 'icon' => 'people-fill', 'href' => route('pc.afad.index', $listFilters)],
        ['label' => 'Total Appointments', 'value' => $summary['total_appointments'], 'icon' => 'calendar3', 'href' => route('pc.appointments.index', $listFilters)],
        ['label' => 'Active Appointments', 'value' => $summary['active_appointments'], 'icon' => 'calendar-check', 'href' => route('pc.appointments.index', $listFilters)],
        ['label' => 'Submitted / Review', 'value' => $summary['submitted_claims'], 'icon' => 'hourglass-split', 'href' => route('pc.claims.index', $claimFilters + ['status' => 'submitted'])],
        ['label' => 'Approved Claims', 'value' => $summary['approved_claims'], 'icon' => 'check2-circle', 'href' => route('pc.claims.index', $claimFilters + ['status' => 'approved'])],
        ['label' => 'PC Endorsed', 'value' => $summary['pc_endorsed_claims'], 'icon' => 'patch-check', 'href' => route('pc.claims.index', $claimFilters + ['endorsement' => 'endorsed'])],
        ['label' => 'Pending Endorsement', 'value' => $summary['pending_pc_endorsements'], 'icon' => 'clipboard-check', 'href' => route('pc.claims.index', $claimFilters + ['endorsement' => 'pending'])],
        ['label' => 'Returned / Rejected', 'value' => $summary['declined_claims'], 'icon' => 'x-circle', 'href' => route('pc.claims.index', $claimFilters + ['status' => 'returned'])],
    ] as $item)
        <div class="col-sm-6 col-xl-3">
            <a href="{{ $item['href'] }}" class="stat-card-link">
                <div class="card stat-card pc-report-stat h-100">
                    <div class="card-body d-flex align-items-center gap-2">
                        <div class="rounded-circle d-flex align-items-center justify-content-center">
                            <i class="bi bi-{{ $item['icon'] }} fs-4"></i>
                        </div>
                        <div>
                            <div class="text-muted small">{{ $item['label'] }}</div>
                            <div class="fw-bold fs-4">{{ $item['value'] }}</div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    @endforeach
</div>

<div class="row g-3 mb-3">
    <div class="col-md-6">
        <div class="card h-100">
            <div class="card-header bg-white fw-semibold">Claim Amount</div>
            <div class="card-body">
                <div class="d-flex justify-content-between border-bottom pb-2 mb-2">
                    <span class="text-muted">Total Claimed</span>
                    <span class="fw-bold">RM {{ number_format($summary['total_claim_amount'], 2) }}</span>
                </div>
                <div class="d-flex justify-content-between">
                    <span class="text-muted">Approved Amount</span>
                    <span class="fw-bold text-success">RM {{ number_format($summary['approved_claim_amount'], 2) }}</span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card h-100">
            <div class="card-header bg-white fw-semibold">Claim Status</div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-sm mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Status</th>
                                <th class="text-end">Claims</th>
                                <th class="text-end">Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($claimStatuses as $row)
                                <tr>
                                    <td><x-status-badge :status="$row->status" /></td>
                                    <td class="text-end">{{ $row->total }}</td>
                                    <td class="text-end">RM {{ number_format($row->amount, 2) }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="3" class="text-center text-muted py-3">No claim data found.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-3 mb-3">
    <div class="col-lg-6">
        <div class="card h-100">
            <div class="card-header bg-white fw-semibold">Claims by Type</div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Type</th>
                                <th class="text-end">Claims</th>
                                <th class="text-end">Hours</th>
                                <th class="text-end">Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($claimTypes as $row)
                                <tr>
                                    <td class="fw-semibold">{{ ucfirst(str_replace('_', ' ', $row->claim_type)) }}</td>
                                    <td class="text-end">{{ $row->total }}</td>
                                    <td class="text-end">{{ number_format($row->hours, 2) }}</td>
                                    <td class="text-end">RM {{ number_format($row->amount, 2) }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="text-center text-muted py-4">No claim type data found.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card h-100">
            <div class="card-header bg-white fw-semibold">Top Appointment Courses</div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Course</th>
                                <th class="text-end">Appointments</th>
                                <th class="text-end">Active</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($appointmentCourses as $row)
                                <tr>
                                    <td>
                                        <div class="fw-semibold">{{ $row->course_code }}</div>
                                        <div class="text-muted small">{{ Str::limit($row->course_name, 48) }}</div>
                                    </td>
                                    <td class="text-end">{{ $row->total }}</td>
                                    <td class="text-end">{{ $row->active_total }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="3" class="text-center text-muted py-4">No appointment data found.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header bg-white fw-semibold">Recent Claims</div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Reference</th>
                        <th>AF/AD</th>
                        <th>Course</th>
                        <th>Type</th>
                        <th>Period</th>
                        <th class="text-end">Amount</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentClaims as $claim)
                        <tr>
                            <td class="fw-semibold">{{ $claim->claim_reference }}</td>
                            <td class="small">{{ $claim->profile?->full_name }}</td>
                            <td class="small">{{ $claim->appointment?->course_code }}</td>
                            <td class="small">{{ ucfirst(str_replace('_', ' ', $claim->claim_type)) }}</td>
                            <td class="small">{{ $claim->period_from?->format('d M Y') }} - {{ $claim->period_to?->format('d M Y') }}</td>
                            <td class="text-end">RM {{ number_format($claim->total_amount, 2) }}</td>
                            <td><x-status-badge :status="$claim->status" /></td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center text-muted py-4">No recent claims found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

</x-layouts.app>
