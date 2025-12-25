<?php

namespace App\Traits;

use Illuminate\Support\Facades\Auth;

trait HasAuditTrail
{
    /**
     * Boot the trait
     */
    public static function bootHasAuditTrail()
    {
        static::creating(function ($model) {
            if (Auth::check()) {
                $model->created_by = Auth::id();
            }
        });

        static::updating(function ($model) {
            if (Auth::check()) {
                $model->updated_by = Auth::id();
            }
        });
    }

    /**
     * Get the user who created this record
     */
    public function creator()
    {
        return $this->belongsTo(config('auth.providers.users.model'), 'created_by');
    }

    /**
     * Get the user who last updated this record
     */
    public function updater()
    {
        return $this->belongsTo(config('auth.providers.users.model'), 'updated_by');
    }

    /**
     * Get creator name
     */
    public function getCreatorNameAttribute(): ?string
    {
        return $this->creator?->name;
    }

    /**
     * Get updater name
     */
    public function getUpdaterNameAttribute(): ?string
    {
        return $this->updater?->name;
    }

    /**
     * Scope to filter by creator
     */
    public function scopeCreatedBy($query, $userId)
    {
        return $query->where('created_by', $userId);
    }

    /**
     * Scope to get records created by current user
     */
    public function scopeCreatedByMe($query)
    {
        return $query->where('created_by', Auth::id());
    }
}
