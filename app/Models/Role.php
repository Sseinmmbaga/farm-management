<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Enums\UserRole;

class Role extends Model
{
    protected $fillable = [
        "name",
        "description",
    ];

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * Get the UserRole enum value for this role
     */
    public function getRoleEnum(): UserRole
    {
        return UserRole::from($this->name);
    }
}
