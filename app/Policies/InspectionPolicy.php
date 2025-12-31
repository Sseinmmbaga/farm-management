<?php

namespace App\Policies;

use App\Models\User;
use App\Models\ICS\Inspection;
use App\Enums\UserRole;
use Illuminate\Auth\Access\HandlesAuthorization;

class InspectionPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any inspections.
     */
    public function viewAny(User $user): bool
    {
        // Admins, supervisors, and ICS inspectors can view inspections
        return $user->hasAnyRole([
            UserRole::ADMIN,
            UserRole::SUPERVISOR,
            UserRole::ICS_INSPECTOR,
        ]);
    }

    /**
     * Determine whether the user can view the inspection.
     */
    public function view(User $user, Inspection $inspection): bool
    {
        // Admins and supervisors can view all inspections
        if ($user->hasRole(UserRole::ADMIN) || $user->hasRole(UserRole::SUPERVISOR)) {
            return true;
        }

        // ICS inspectors can view their own inspections
        if ($user->hasRole(UserRole::ICS_INSPECTOR)) {
            return $inspection->inspector_id === $user->id;
        }

        return false;
    }

    /**
     * Determine whether the user can create inspections.
     */
    public function create(User $user): bool
    {
        return $user->hasAnyRole([
            UserRole::ADMIN,
            UserRole::SUPERVISOR,
            UserRole::ICS_INSPECTOR,
        ]);
    }

    /**
     * Determine whether the user can update the inspection.
     */
    public function update(User $user, Inspection $inspection): bool
    {
        // Admins and supervisors can update all inspections
        if ($user->hasRole(UserRole::ADMIN) || $user->hasRole(UserRole::SUPERVISOR)) {
            return true;
        }

        // ICS inspectors can update their own inspections
        if ($user->hasRole(UserRole::ICS_INSPECTOR)) {
            return $inspection->inspector_id === $user->id;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the inspection.
     */
    public function delete(User $user, Inspection $inspection): bool
    {
        // Only admins and supervisors can delete inspections
        return $user->hasRole(UserRole::ADMIN) || $user->hasRole(UserRole::SUPERVISOR);
    }

    /**
     * Determine whether the user can restore the inspection.
     */
    public function restore(User $user, Inspection $inspection): bool
    {
        return $user->hasRole(UserRole::ADMIN);
    }

    /**
     * Determine whether the user can permanently delete the inspection.
     */
    public function forceDelete(User $user, Inspection $inspection): bool
    {
        return $user->hasRole(UserRole::ADMIN);
    }
}
