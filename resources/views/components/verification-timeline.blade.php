@props(['profile' => null])

@php
    $items = collect();

    if (!$profile) {
        $items->push([
            'tone' => 'warning',
            'title' => 'Profile Registration Required',
            'time' => now(),
            'message' => 'Complete your AF/AD profile to begin the School Executive verification process.',
            'action' => ['label' => 'Register Profile', 'url' => route('afad.profile.create')],
        ]);
    } else {
        $items->push([
            'tone' => 'muted',
            'title' => 'Profile Initialized',
            'time' => $profile->created_at,
            'message' => 'Your AF/AD profile has been submitted for verification.',
        ]);

        if ($profile->status === 'rejected') {
            $sectionLabels = [
                'personal' => 'Personal Information',
                'qualification' => 'Qualification',
                'bank' => 'Bank Information',
                'resume' => 'Resume / CV',
                'certificates' => 'Certificates',
                'other' => 'Other',
            ];
            $sections = collect($profile->rejection_sections ?? [])
                ->map(fn ($section) => $sectionLabels[$section] ?? ucfirst($section))
                ->implode(', ');

            $items->push([
                'tone' => 'danger',
                'title' => 'Action Requested',
                'time' => $profile->verified_at ?? $profile->updated_at,
                'message' => trim(($sections ? "Update {$sections}. " : '') . ($profile->rejection_reason ?? 'Please update your profile and resubmit it for verification.')),
                'action' => ['label' => 'Update Profile', 'url' => route('afad.profile.edit')],
            ]);
        } elseif ($profile->status === 'verified') {
            $items->push([
                'tone' => 'success',
                'title' => 'Profile Verified',
                'time' => $profile->verified_at ?? $profile->updated_at,
                'message' => 'Your profile has been verified by the School Executive. You may now proceed with AF/AD activities.',
            ]);
        } else {
            $items->push([
                'tone' => 'warning',
                'title' => 'Verification Underway',
                'time' => $profile->updated_at ?? $profile->created_at,
                'message' => 'The School Executive is currently reviewing your profile and supporting documents.',
            ]);
        }
    }

    $items = $items->reverse()->values();
@endphp

<div class="card verification-timeline-card">
    <div class="card-header bg-white d-flex align-items-center gap-2">
        <i class="bi bi-clock-history text-primary"></i>
        <span class="fw-semibold">Verification Timeline</span>
    </div>
    <div class="card-body">
        <div class="verification-timeline">
            @foreach($items as $item)
                <div class="verification-timeline-item verification-timeline-{{ $item['tone'] }}">
                    <div class="verification-timeline-marker"></div>
                    <div class="verification-timeline-content">
                        <div class="fw-semibold">{{ $item['title'] }}</div>
                        <div class="text-muted small">{{ $item['time']?->format('d M Y - h:i A') }}</div>
                        @if(!empty($item['message']))
                            <div class="verification-timeline-message mt-2">
                                {{ $item['message'] }}
                            </div>
                        @endif
                        @if(!empty($item['action']))
                            <a href="{{ $item['action']['url'] }}" class="btn btn-sm btn-outline-primary mt-2">{{ $item['action']['label'] }}</a>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>

@once
    <style>
        .verification-timeline-card {
            background: #fffafa;
        }
        .verification-timeline {
            position: relative;
            display: grid;
            gap: 1.65rem;
        }
        .verification-timeline-item {
            position: relative;
            display: grid;
            grid-template-columns: 42px 1fr;
            gap: 1rem;
        }
        .verification-timeline-item::before {
            content: "";
            position: absolute;
            top: 28px;
            bottom: -1.65rem;
            left: 20px;
            width: 3px;
            background: #f0cfcb;
        }
        .verification-timeline-item:last-child::before {
            display: none;
        }
        .verification-timeline-marker {
            width: 32px;
            height: 32px;
            margin: 0.1rem auto 0;
            border-radius: 50%;
            border: 7px solid #fde8e6;
            background: #e8c4bf;
            box-shadow: 0 0 0 1px rgba(195, 18, 31, 0.08);
            z-index: 1;
        }
        .verification-timeline-warning .verification-timeline-marker {
            background: #f59f00;
            border-color: #fff0c2;
        }
        .verification-timeline-danger .verification-timeline-marker {
            background: #c3121f;
            border-color: #fde2e4;
        }
        .verification-timeline-success .verification-timeline-marker {
            background: #198754;
            border-color: #d9f2e4;
        }
        .verification-timeline-content {
            min-width: 0;
        }
        .verification-timeline-message {
            border-left: 5px solid #f59f00;
            background: #fde1de;
            padding: 0.85rem 1rem;
            color: var(--portal-ink);
            line-height: 1.55;
        }
        .verification-timeline-danger .verification-timeline-message {
            border-left-color: #c3121f;
        }
        .verification-timeline-success .verification-timeline-message {
            border-left-color: #198754;
            background: #e7f5ec;
        }
        .verification-timeline-muted .verification-timeline-message {
            border-left-color: #e8c4bf;
            background: #fff3f2;
        }
    </style>
@endonce
