@props(['submissions'])

@php
    $submissionList = collect($submissions instanceof \Illuminate\Pagination\AbstractPaginator ? $submissions->items() : $submissions);
@endphp

<div class="card submission-documents-card">
    <div class="card-header bg-white d-flex align-items-center justify-content-between gap-2">
        <div class="d-flex align-items-center gap-2">
            <i class="bi bi-folder-check text-primary"></i>
            <span class="fw-semibold">Submission Documents</span>
        </div>
        <span class="badge bg-light text-dark border">{{ $submissionList->count() }}</span>
    </div>
    <div class="card-body">
        @if($submissionList->isEmpty())
            <div class="text-center text-muted py-4">
                <i class="bi bi-file-earmark-plus fs-2 d-block mb-2"></i>
                No documents submitted yet.
                <div class="mt-2">
                    <a href="{{ route('afad.submissions.create') }}" class="btn btn-sm btn-outline-primary">New Submission</a>
                </div>
            </div>
        @else
            <div class="submission-documents-scroll">
                @foreach($submissionList as $submission)
                    <a href="{{ route('afad.submissions.show', $submission) }}" class="submission-document-item">
                        <div class="submission-document-icon">
                            @if($submission->hasVideoLink())
                                <i class="bi bi-link-45deg"></i>
                            @elseif(str_contains($submission->file_mime ?? '', 'pdf'))
                                <i class="bi bi-file-earmark-pdf"></i>
                            @elseif(str_starts_with($submission->file_mime ?? '', 'video/'))
                                <i class="bi bi-camera-video"></i>
                            @else
                                <i class="bi bi-file-earmark-text"></i>
                            @endif
                        </div>
                        <div class="flex-grow-1 min-w-0">
                            <div class="fw-semibold text-truncate">{{ $submission->title }}</div>
                            <div class="text-muted small text-truncate">
                                {{ $submission->type_label }} &bull; {{ $submission->course ?? 'No course' }}
                            </div>
                            <div class="text-muted small text-truncate">
                                {{ $submission->hasVideoLink() ? 'Video recording link' : $submission->file_original_name }}
                            </div>
                        </div>
                        <div class="text-end flex-shrink-0">
                            @if($submission->status === 'reviewed')
                                <span class="badge bg-success">Reviewed</span>
                            @else
                                <span class="badge bg-warning text-dark">Pending</span>
                            @endif
                            <div class="text-muted small mt-1">{{ ($submission->submission_date ?? $submission->created_at)->format('d M Y') }}</div>
                        </div>
                    </a>
                @endforeach
            </div>
        @endif
    </div>
</div>

@once
    <style>
        .submission-documents-card {
            background: #fffafa;
        }
        .submission-documents-scroll {
            max-height: 360px;
            overflow-y: auto;
            padding-right: 0.35rem;
        }
        .submission-documents-scroll::-webkit-scrollbar {
            width: 8px;
        }
        .submission-documents-scroll::-webkit-scrollbar-thumb {
            background: #e8c4bf;
            border-radius: 999px;
        }
        .submission-document-item {
            display: flex;
            align-items: center;
            gap: 0.85rem;
            padding: 0.85rem 0;
            color: inherit;
            text-decoration: none;
            border-bottom: 1px solid #f2d5d1;
        }
        .submission-document-item:last-child {
            border-bottom: 0;
        }
        .submission-document-item:hover,
        .submission-document-item:focus {
            color: inherit;
            text-decoration: none;
        }
        .submission-document-item:hover .fw-semibold,
        .submission-document-item:focus .fw-semibold {
            color: var(--portal-red);
        }
        .submission-document-icon {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: #fde8e6;
            color: var(--portal-red);
            flex: 0 0 38px;
        }
        .min-w-0 {
            min-width: 0;
        }
    </style>
@endonce
