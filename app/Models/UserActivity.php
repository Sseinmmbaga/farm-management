<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserActivity extends Model
{
    protected $fillable = [
        'user_id',
        'action',
        'performed_by',
        'ip_address',
        'details',
    ];

    protected $casts = [
        'details' => 'array',
    ];

    // ==================== RELATIONSHIPS ====================

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function performedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'performed_by');
    }

    // ==================== SCOPES ====================

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeRecentFirst($query)
    {
        return $query->orderBy('created_at', 'desc');
    }

    public function scopeOfAction($query, $action)
    {
        return $query->where('action', $action);
    }

    // ==================== HELPERS ====================

    public function getActionLabelAttribute(): string
    {
        return match($this->action) {
            'created' => 'Account Created',
            'updated' => 'Profile Updated',
            'password_reset' => 'Password Reset',
            'activated' => 'Account Activated',
            'deactivated' => 'Account Deactivated',
            'login' => 'Logged In',
            'deleted' => 'Account Deleted',
            default => ucfirst($this->action),
        };
    }

    public function getActionIconAttribute(): string
    {
        return match($this->action) {
            'created' => 'fa-user-plus',
            'updated' => 'fa-user-edit',
            'password_reset' => 'fa-key',
            'activated' => 'fa-user-check',
            'deactivated' => 'fa-user-slash',
            'login' => 'fa-sign-in-alt',
            'deleted' => 'fa-user-times',
            default => 'fa-circle',
        };
    }

    public function getActionColorAttribute(): string
    {
        return match($this->action) {
            'created' => 'success',
            'updated' => 'info',
            'password_reset' => 'warning',
            'activated' => 'success',
            'deactivated' => 'danger',
            'login' => 'primary',
            'deleted' => 'danger',
            default => 'secondary',
        };
    }

    public static function log(User $user, string $action, ?User $performedBy = null, ?array $details = null): self
    {
        return self::create([
            'user_id' => $user->id,
            'action' => $action,
            'performed_by' => $performedBy?->id,
            'ip_address' => request()->ip(),
            'details' => $details,
        ]);
    }
}
