@props(['status'])

@php
$config = match($status) {
    'pending'        => ['bg-warning text-dark', 'hourglass-split', 'Pending'],
    'verified'       => ['bg-success',           'check-circle-fill', 'Verified'],
    'rejected'       => ['bg-danger',            'x-circle-fill', 'Rejected'],
    'draft'          => ['bg-secondary',          'pencil', 'Draft'],
    'submitted'      => ['bg-primary',            'send-fill', 'Pending Review'],
    'under_review'   => ['bg-info',               'eye-fill', 'Under Review'],
    'approved'       => ['bg-success',            'check-circle-fill', 'Approved'],
    'returned'       => ['bg-warning text-dark',  'arrow-counterclockwise', 'Returned'],
    'not_registered' => ['bg-light text-dark border', 'person-x', 'Not Registered'],
    default          => ['bg-secondary',           'dash-circle', ucfirst($status)],
};
@endphp

<span class="badge {{ $config[0] }} d-inline-flex align-items-center gap-1">
    <i class="bi bi-{{ $config[1] }}"></i> {{ $config[2] }}
</span>
