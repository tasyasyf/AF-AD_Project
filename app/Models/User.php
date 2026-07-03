<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

#[Fillable(['name', 'email', 'password', 'role', 'is_active', 'permissions', 'additional_access_enabled', 'email_verified_at', 'profile_photo_path', 'profile_photo_original_name'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'permissions' => 'array',
            'additional_access_enabled' => 'boolean',
        ];
    }

    /**
     * Read-only "Additional Access" function keys granted to this user.
     *
     * @return array<int, string>
     */
    public function grantedPermissions(): array
    {
        return $this->permissions ?? [];
    }

    /**
     * Whether this user may VIEW the given additional-access function.
     * Admins bypass; the master toggle can disable everything at once.
     */
    public function hasPermission(string $key): bool
    {
        if ($this->isAdmin()) {
            return true;
        }

        if (!$this->additional_access_enabled) {
            return false;
        }

        return in_array($key, $this->grantedPermissions(), true);
    }

    /**
     * Whether the "Additional Access" sidebar group should appear at all.
     */
    public function hasAnyAdditionalAccess(): bool
    {
        return $this->additional_access_enabled && count($this->grantedPermissions()) > 0;
    }

    public function profile(): HasOne
    {
        return $this->hasOne(Profile::class);
    }

    public function claimAudits(): HasMany
    {
        return $this->hasMany(ClaimAudit::class, 'performed_by');
    }

    public function reviewedClaims(): HasMany
    {
        return $this->hasMany(Claim::class, 'reviewed_by');
    }

    public function verifiedProfiles(): HasMany
    {
        return $this->hasMany(Profile::class, 'verified_by');
    }

    public function isAfAd(): bool
    {
        return $this->role === 'afad';
    }

    public function isExecutive(): bool
    {
        return $this->role === 'executive';
    }

    public function isProgramCoordinator(): bool
    {
        return $this->role === 'pc';
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }
}
