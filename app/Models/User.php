<?php

namespace App\Models;

use App\Enums\UserRole;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'phone',
        'is_active',
        'avatar',
        'last_login_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => UserRole::class,
            'is_active' => 'boolean',
            'last_login_at' => 'datetime',
        ];
    }

    // Scopes
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeExtensionOfficers(Builder $query): Builder
    {
        return $query->where('role', UserRole::EXTENSION_OFFICER);
    }

    public function scopeInactive(Builder $query): Builder
    {
        return $query->where('is_active', false);
    }

    public function scopeByRole(Builder $query, $role): Builder
    {
        if ($role instanceof UserRole) {
            return $query->where('role', $role);
        }
        return $query->where('role', $role);
    }

    public function scopeSearch(Builder $query, ?string $search): Builder
    {
        if (!$search) {
            return $query;
        }

        return $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('email', 'like', "%{$search}%")
              ->orWhere('phone', 'like', "%{$search}%");
        });
    }

    // ==================== RELATIONSHIPS ====================

    public function activities(): HasMany
    {
        return $this->hasMany(UserActivity::class);
    }

    public function assignedFarmers(): HasMany
    {
        return $this->hasMany(\App\Models\Farmers\Farmer::class, 'extension_officer_id');
    }

    public function notificationPreference(): HasOne
    {
        return $this->hasOne(\App\Models\Notifications\NotificationPreference::class);
    }

    public function notificationLogs(): HasMany
    {
        return $this->hasMany(\App\Models\Notifications\NotificationLog::class);
    }

    // ==================== NOTIFICATION HELPERS ====================

    public function getNotificationPreferences(): \App\Models\Notifications\NotificationPreference
    {
        return \App\Models\Notifications\NotificationPreference::getOrCreateForUser($this);
    }

    public function shouldReceiveNotification(string $category, string $channel = 'database'): bool
    {
        return $this->getNotificationPreferences()->shouldNotify($category, $channel);
    }

    public function routeNotificationForMail(): ?string
    {
        return $this->email;
    }

    public function routeNotificationForSms(): ?string
    {
        return $this->phone;
    }

    // Role check helpers
    public function isAdmin(): bool
    {
        return $this->role === UserRole::ADMIN;
    }

    public function isSupervisor(): bool
    {
        return $this->role === UserRole::SUPERVISOR;
    }

    public function isExtensionOfficer(): bool
    {
        return $this->role === UserRole::EXTENSION_OFFICER;
    }

    public function isIcsInspector(): bool
    {
        return $this->role === UserRole::ICS_INSPECTOR;
    }

    public function isStockManager(): bool
    {
        return $this->role === UserRole::STOCK_MANAGER;
    }

    public function isAccountant(): bool
    {
        return $this->role === UserRole::ACCOUNTANT;
    }

    public function isTrainingCoordinator(): bool
    {
        return $this->role === UserRole::TRAINING_COORDINATOR;
    }

    public function isProductionManager(): bool
    {
        return $this->role === UserRole::PRODUCTION_MANAGER;
    }

    public function isFarmer(): bool
    {
        return $this->role === UserRole::FARMER;
    }

    /**
     * Check if user has view-only access to data (no edit/delete permissions).
     * Applies to ICS Inspectors and Training Coordinators.
     */
    public function hasViewOnlyAccess(): bool
    {
        return $this->isIcsInspector() || $this->isTrainingCoordinator();
    }

    public function hasRole(UserRole $role): bool
    {
        return $this->role === $role;
    }

    public function hasAnyRole(array $roles): bool
    {
        return in_array($this->role, $roles);
    }

    public function getDashboardRoute(): string
    {
        return $this->role->dashboardRoute();
    }

    public function getRoleLabel(): string
    {
        return $this->role?->label() ?? 'User';
    }

    public function getRoleValue(): string
    {
        return $this->role?->value ?? 'default';
    }
}
