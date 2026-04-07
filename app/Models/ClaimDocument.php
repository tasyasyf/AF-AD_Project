<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'claim_id', 'document_type', 'label', 'file_path',
    'file_original_name', 'file_mime', 'file_size',
    'is_required', 'is_uploaded', 'uploaded_at', 'notes', 'sort_order',
])]
class ClaimDocument extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'is_required' => 'boolean',
            'is_uploaded' => 'boolean',
            'uploaded_at' => 'datetime',
        ];
    }

    public function claim(): BelongsTo
    {
        return $this->belongsTo(Claim::class);
    }
}
