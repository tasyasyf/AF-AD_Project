<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Http\Request;

#[Fillable([
    'claim_id', 'performed_by', 'action', 'from_status',
    'to_status', 'remarks', 'metadata', 'ip_address', 'user_agent',
])]
class ClaimAudit extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected function casts(): array
    {
        return [
            'metadata' => 'array',
            'created_at' => 'datetime',
        ];
    }

    public function claim(): BelongsTo
    {
        return $this->belongsTo(Claim::class);
    }

    public function performer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'performed_by');
    }

    public static function record(
        Claim $claim,
        string $action,
        ?string $fromStatus,
        ?string $toStatus,
        ?string $remarks = null,
        array $meta = []
    ): self {
        $request = app(Request::class);
        return static::create([
            'claim_id'    => $claim->id,
            'performed_by'=> auth()->id(),
            'action'      => $action,
            'from_status' => $fromStatus,
            'to_status'   => $toStatus,
            'remarks'     => $remarks,
            'metadata'    => $meta ?: null,
            'ip_address'  => $request->ip(),
            'user_agent'  => $request->userAgent(),
        ]);
    }
}
