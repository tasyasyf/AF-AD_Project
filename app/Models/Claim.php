<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'appointment_id', 'profile_id', 'claim_reference', 'claim_type', 'claim_items', 'claim_form_data',
    'period_from', 'period_to', 'total_hours', 'rate_per_hour', 'total_amount',
    'has_mark_entry_forms', 'has_graded_scripts', 'has_qa', 'has_question_bank_answer_sheet',
    'status', 'submitted_at', 'reviewed_by', 'reviewed_at',
    'pc_endorsed_by', 'pc_endorsed_at', 'pc_remarks',
    'executive_remarks', 'payment_reference', 'paid_at',
])]
class Claim extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'period_from' => 'date',
            'period_to' => 'date',
            'submitted_at' => 'datetime',
            'reviewed_at' => 'datetime',
            'pc_endorsed_at' => 'datetime',
            'paid_at' => 'datetime',
            'claim_items' => 'array',
            'claim_form_data' => 'array',
            'total_hours' => 'decimal:2',
            'rate_per_hour' => 'decimal:2',
            'total_amount' => 'decimal:2',
            'has_mark_entry_forms' => 'boolean',
            'has_graded_scripts' => 'boolean',
            'has_qa' => 'boolean',
            'has_question_bank_answer_sheet' => 'boolean',
        ];
    }

    protected static function boot(): void
    {
        parent::boot();
        static::creating(function (Claim $claim) {
            $year = date('Y');
            $count = static::whereYear('created_at', $year)->count() + 1;
            $claim->claim_reference = 'CLAIM-' . $year . '-' . str_pad($count, 5, '0', STR_PAD_LEFT);
        });
    }

    public function appointment(): BelongsTo
    {
        return $this->belongsTo(Appointment::class);
    }

    public function profile(): BelongsTo
    {
        return $this->belongsTo(Profile::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function pcEndorser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'pc_endorsed_by');
    }

    public function documents(): HasMany
    {
        return $this->hasMany(ClaimDocument::class)->orderBy('sort_order');
    }

    public function audits(): HasMany
    {
        return $this->hasMany(ClaimAudit::class)->orderBy('created_at', 'desc');
    }

    public function submissions(): HasMany
    {
        return $this->hasMany(Submission::class);
    }

    public function displayClaimItems(): array
    {
        if (is_array($this->claim_items) && count($this->claim_items) > 0) {
            return $this->claim_items;
        }

        return [[
            'claim_type' => $this->claim_type,
            'total_hours' => (float) $this->total_hours,
            'rate' => (float) $this->rate_per_hour,
            'amount' => (float) $this->total_amount,
        ]];
    }

    public function scopeDraft(Builder $query): Builder        { return $query->where('status', 'draft'); }
    public function scopeSubmitted(Builder $query): Builder    { return $query->where('status', 'submitted'); }
    public function scopeUnderReview(Builder $query): Builder  { return $query->where('status', 'under_review'); }
    public function scopeApproved(Builder $query): Builder     { return $query->where('status', 'approved'); }
    public function scopeReturned(Builder $query): Builder     { return $query->where('status', 'returned'); }
    public function scopeRejected(Builder $query): Builder     { return $query->where('status', 'rejected'); }
}
