<x-layouts.app title="Submission Detail">

<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="fw-bold mb-0">{{ $submission->title }}</h5>
    <a href="{{ route('afad.submissions.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i> Back
    </a>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card mb-4">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <span class="fw-semibold">Submission Details</span>
                @if($submission->status === 'reviewed')
                    <span class="badge bg-success">Reviewed</span>
                @else
                    <span class="badge bg-warning text-dark">Pending Review</span>
                @endif
            </div>
            <div class="card-body">
                <dl class="row mb-0">
                    <dt class="col-sm-3 text-muted">Title</dt>
                    <dd class="col-sm-9">{{ $submission->title }}</dd>
                    <dt class="col-sm-3 text-muted">Type</dt>
                    <dd class="col-sm-9">{{ $submission->type_label }}</dd>
                    <dt class="col-sm-3 text-muted">Course Code</dt>
                    <dd class="col-sm-9">{{ $submission->course ?? '—' }}</dd>
                    <dt class="col-sm-3 text-muted">Course Name</dt>
                    <dd class="col-sm-9">{{ $submission->course_name ?? '—' }}</dd>
                    <dt class="col-sm-3 text-muted">Programme</dt>
                    <dd class="col-sm-9">{{ $submission->programme ?? '—' }}</dd>
                    <dt class="col-sm-3 text-muted">Submission Date</dt>
                    <dd class="col-sm-9">{{ ($submission->submission_date ?? $submission->created_at)->format('d M Y') }}</dd>
                    @if($submission->isVideoRecording())
                        <dt class="col-sm-3 text-muted">Claim Hours</dt>
                        <dd class="col-sm-9" id="claim-hours-value">{{ $submission->claim_hours ? number_format($submission->claim_hours, 2) : '—' }}</dd>
                        <dt class="col-sm-3 text-muted">Rate per Hour</dt>
                        <dd class="col-sm-9">{{ $submission->rate_per_hour ? 'RM ' . number_format($submission->rate_per_hour, 2) : '—' }}</dd>
                        <dt class="col-sm-3 text-muted">Total Amount</dt>
                        <dd class="col-sm-9" id="total-amount-value">{{ $submission->total_amount ? 'RM ' . number_format($submission->total_amount, 2) : '—' }}</dd>
                        <dt class="col-sm-3 text-muted">Tutorial</dt>
                        <dd class="col-sm-9">Tutorial {{ $submission->tutorial_number ?? '—' }}</dd>
                        @if($submission->hasVideoLink())
                            <dt class="col-sm-3 text-muted">Total Duration</dt>
                            <dd class="col-sm-9" id="duration-value">{{ $submission->video_duration_minutes ? number_format($submission->video_duration_minutes, 2) . ' minutes' : 'Calculating...' }}</dd>
                            <dt class="col-sm-3 text-muted">Video Link</dt>
                            <dd class="col-sm-9">
                                <a href="{{ $submission->video_link }}" target="_blank" rel="noopener" class="text-decoration-none">
                                    {{ $submission->video_link }}
                                </a>
                            </dd>
                        @else
                            <dt class="col-sm-3 text-muted">Total Duration</dt>
                            <dd class="col-sm-9">{{ $submission->video_duration_minutes ? number_format($submission->video_duration_minutes, 2) . ' minutes' : '—' }}</dd>
                        @endif
                    @endif
                    @if($submission->isQuestionBankAnswerSheet())
                        <dt class="col-sm-3 text-muted">Semester Intake</dt>
                        <dd class="col-sm-9">{{ $submission->semester_intake ?? '—' }}</dd>
                        <dt class="col-sm-3 text-muted">PC QB-AS Check</dt>
                        <dd class="col-sm-9">
                            <span class="badge {{ $submission->pc_qbas_status_badge_class }}">
                                {{ $submission->pc_qbas_status_label }}
                            </span>
                            @if($submission->pc_qbas_set_count)
                                <span class="text-muted small ms-2">{{ $submission->pc_qbas_set_count }} set recorded</span>
                            @endif
                            @if($submission->pc_qbas_checked_at)
                                <div class="text-muted small mt-1">
                                    Checked on {{ $submission->pc_qbas_checked_at->format('d M Y H:i') }}
                                </div>
                            @endif
                            @if($submission->pc_qbas_remarks)
                                <div class="small mt-1">{{ $submission->pc_qbas_remarks }}</div>
                            @endif
                        </dd>
                    @endif
                    <dt class="col-sm-3 text-muted">Description</dt>
                    <dd class="col-sm-9">{{ $submission->description ?? '—' }}</dd>
                    <dt class="col-sm-3 text-muted">Uploaded At</dt>
                    <dd class="col-sm-9">{{ $submission->created_at->format('d M Y H:i') }}</dd>
                </dl>
            </div>
        </div>

        <div class="card">
            <div class="card-header bg-white fw-semibold">{{ $submission->hasVideoLink() ? 'Video Recording Link' : 'Uploaded File' }}</div>
            <div class="card-body">
                <div class="d-flex align-items-center gap-3">
                    <i class="bi bi-{{ $submission->hasVideoLink() ? 'link-45deg text-primary' : (str_starts_with($submission->file_mime, 'video/') ? 'camera-video text-danger' : (str_contains($submission->file_mime, 'pdf') ? 'file-earmark-pdf text-danger' : 'file-earmark-text text-primary')) }} fs-2"></i>
                    <div class="flex-grow-1">
                        <div class="fw-semibold">{{ $submission->hasVideoLink() ? 'Video recording link' : $submission->file_original_name }}</div>
                        <div class="text-muted small text-break">
                            {{ $submission->hasVideoLink() ? $submission->video_link : number_format($submission->file_size / 1024, 1) . ' KB' }}
                        </div>
                    </div>
                    @if($submission->hasVideoLink())
                        <a href="{{ $submission->video_link }}" target="_blank" rel="noopener" class="btn btn-outline-primary">
                            <i class="bi bi-box-arrow-up-right me-1"></i> Open Link
                        </a>
                    @else
                        <a href="{{ route('afad.submissions.download', $submission) }}" class="btn btn-outline-primary">
                            <i class="bi bi-download me-1"></i> Download
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        @if($submission->status === 'reviewed')
            <div class="card">
                <div class="card-header bg-white fw-semibold">Executive Review</div>
                <div class="card-body">
                    <div class="small mb-2">
                        <strong>Reviewed by:</strong> {{ $submission->reviewer?->name }}<br>
                        <strong>On:</strong> {{ $submission->reviewed_at?->format('d M Y H:i') }}
                    </div>
                    @if($submission->executive_remarks)
                        <hr>
                        <div class="text-muted small">Remarks:</div>
                        <div class="small">{{ $submission->executive_remarks }}</div>
                    @endif
                </div>
            </div>
        @else
            <div class="card">
                <div class="card-header bg-white fw-semibold">Status</div>
                <div class="card-body text-center">
                    <i class="bi bi-clock-history fs-1 text-warning"></i>
                    <p class="text-muted small mt-2 mb-0">Awaiting review by the School Executive.</p>
                </div>
            </div>
        @endif
    </div>
</div>

@if($submission->hasVideoLink())
    <div id="youtube-duration-player" class="d-none"></div>
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const videoUrl = @json($submission->video_link);
        const alreadyHasDuration = @json((bool) $submission->video_duration_minutes);
        const durationEl = document.getElementById('duration-value');
        const claimHoursEl = document.getElementById('claim-hours-value');
        const totalAmountEl = document.getElementById('total-amount-value');

        if (alreadyHasDuration || !videoUrl) {
            return;
        }

        function updateDisplay(data) {
            durationEl.textContent = data.formatted_duration;
            claimHoursEl.textContent = data.formatted_claim_hours;
            totalAmountEl.textContent = data.formatted_total_amount;
        }

        function persistDuration(seconds) {
            if (!Number.isFinite(seconds) || seconds <= 0) {
                durationEl.textContent = '—';
                return;
            }

            fetch(@json(route('afad.submissions.duration', $submission)), {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': @json(csrf_token()),
                },
                body: JSON.stringify({ video_duration_seconds: Math.round(seconds) }),
            })
                .then(response => response.ok ? response.json() : Promise.reject())
                .then(updateDisplay)
                .catch(() => {
                    durationEl.textContent = '—';
                });
        }

        function youtubeId(url) {
            try {
                const parsed = new URL(url);
                if (parsed.hostname === 'youtu.be') {
                    return parsed.pathname.replace('/', '');
                }
                if (parsed.hostname.includes('youtube.com')) {
                    if (parsed.searchParams.get('v')) {
                        return parsed.searchParams.get('v');
                    }
                    const embedMatch = parsed.pathname.match(/\/(?:embed|shorts)\/([^/?]+)/);
                    return embedMatch ? embedMatch[1] : null;
                }
            } catch (error) {
                return null;
            }

            return null;
        }

        const youtubeVideoId = youtubeId(videoUrl);

        if (youtubeVideoId) {
            window.onYouTubeIframeAPIReady = function () {
                const player = new YT.Player('youtube-duration-player', {
                    videoId: youtubeVideoId,
                    events: {
                        onReady: function () {
                            setTimeout(() => persistDuration(player.getDuration()), 600);
                        },
                    },
                });
            };

            const script = document.createElement('script');
            script.src = 'https://www.youtube.com/iframe_api';
            document.head.appendChild(script);
            return;
        }

        const video = document.createElement('video');
        video.preload = 'metadata';
        video.onloadedmetadata = function () {
            persistDuration(video.duration);
        };
        video.onerror = function () {
            durationEl.textContent = '—';
        };
        video.src = videoUrl;
    });
    </script>
@endif

</x-layouts.app>
