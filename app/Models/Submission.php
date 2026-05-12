<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'profile_id', 'claim_id', 'submission_type', 'submission_date', 'title', 'description',
    'file_path', 'file_original_name', 'file_mime', 'file_size', 'video_url',
    'video_duration_minutes', 'tutorial_number',
    'claim_hours', 'rate_per_hour', 'total_amount',
    'semester_intake', 'course', 'course_name', 'programme',
    'status', 'reviewed_by', 'reviewed_at', 'executive_remarks',
    'pc_qbas_status', 'pc_qbas_set_count', 'pc_qbas_checked_by', 'pc_qbas_checked_at', 'pc_qbas_remarks',
])]
class Submission extends Model
{
    use HasFactory;

    public const TYPE_VIDEO_RECORDING = 'video_recording';
    public const TYPE_ATTENDANCE_SHEET = 'attendance_sheet';
    public const TYPE_MARK_ENTRY_FORMS = 'mark_entry_forms';
    public const TYPE_QA = 'qa';
    public const TYPE_QUESTION_BANK_ANSWER_SHEET = 'question_bank_answer_sheet';

    public const TYPES = [
        self::TYPE_VIDEO_RECORDING => 'Video Recording Submission',
        self::TYPE_ATTENDANCE_SHEET => 'Attendance Sheet',
        self::TYPE_MARK_ENTRY_FORMS => 'Mark-entry Forms',
        self::TYPE_QA => 'Question Paper & Answer Sheet',
        self::TYPE_QUESTION_BANK_ANSWER_SHEET => 'QB-AS',
    ];

    public const CLAIM_CHECKLIST_MAP = [
        self::TYPE_MARK_ENTRY_FORMS => 'has_mark_entry_forms',
        self::TYPE_QA => 'has_qa',
        self::TYPE_QUESTION_BANK_ANSWER_SHEET => 'has_question_bank_answer_sheet',
    ];

    protected function casts(): array
    {
        return [
            'submission_date' => 'date',
            'video_duration_minutes' => 'decimal:2',
            'claim_hours' => 'decimal:2',
            'rate_per_hour' => 'decimal:2',
            'total_amount' => 'decimal:2',
            'reviewed_at' => 'datetime',
            'pc_qbas_checked_at' => 'datetime',
        ];
    }

    public function profile(): BelongsTo
    {
        return $this->belongsTo(Profile::class);
    }

    public function claim(): BelongsTo
    {
        return $this->belongsTo(Claim::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function pcQbasChecker(): BelongsTo
    {
        return $this->belongsTo(User::class, 'pc_qbas_checked_by');
    }

    public function getTypeLabelAttribute(): string
    {
        return self::TYPES[$this->submission_type] ?? ucfirst(str_replace('_', ' ', $this->submission_type));
    }

    public function isPdfSubmission(): bool
    {
        return $this->isAttendanceSheet() || in_array($this->submission_type, array_keys(self::CLAIM_CHECKLIST_MAP), true);
    }

    public function isVideoRecording(): bool
    {
        return $this->submission_type === self::TYPE_VIDEO_RECORDING;
    }

    public function isAttendanceSheet(): bool
    {
        return $this->submission_type === self::TYPE_ATTENDANCE_SHEET;
    }

    public function getVideoLinkAttribute(): ?string
    {
        if (!$this->isVideoRecording()) {
            return null;
        }

        foreach ([$this->video_url, $this->file_path] as $url) {
            if (is_string($url) && filter_var($url, FILTER_VALIDATE_URL)) {
                return $url;
            }
        }

        return null;
    }

    public function hasVideoLink(): bool
    {
        return (bool) $this->video_link;
    }

    public function isQuestionBankAnswerSheet(): bool
    {
        return $this->submission_type === self::TYPE_QUESTION_BANK_ANSWER_SHEET;
    }

    public function getPcQbasStatusLabelAttribute(): string
    {
        return match ($this->pc_qbas_status) {
            'confirmed' => 'PC Confirmed',
            'rejected' => 'PC Rejected',
            default => 'Check on Pending',
        };
    }

    public function getPcQbasStatusBadgeClassAttribute(): string
    {
        return match ($this->pc_qbas_status) {
            'confirmed' => 'bg-success',
            'rejected' => 'bg-danger',
            default => 'bg-warning text-dark',
        };
    }

    public function isMarkEntryForms(): bool
    {
        return $this->submission_type === self::TYPE_MARK_ENTRY_FORMS;
    }
}
