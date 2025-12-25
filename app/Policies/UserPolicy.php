<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole([UserRole::ADMIN, UserRole::SUPERVISOR]);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, User $model): bool
    {
        // Admin can view all users
        if ($user->isAdmin()) {
            return true;
        }

        // Supervisor can view non-admin users
        if ($user->isSupervisor()) {
            return !$model->isAdmin();
        }

        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, User $model): bool
    {
        // Admin can update all users
        if ($user->isAdmin()) {
            return true;
        }

        // Supervisor can update non-admin users
        if ($user->isSupervisor()) {
            return !$model->isAdmin();
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, User $model): bool
    {
        // Only admin can delete
        if (!$user->isAdmin()) {
            return false;
        }

        // Can't delete yourself
        if ($user->id === $model->id) {
            return false;
        }

        return true;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, User $model): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, User $model): bool
    {
        return $user->isAdmin() && $user->id !== $model->id;
    }

    /**
     * Determine whether the user can reset the password.
     */
    public function resetPassword(User $user, User $model): bool
    {
        // Only admin can reset passwords
        if (!$user->isAdmin()) {
            return false;
        }

        // Can't reset your own password through this method
        if ($user->id === $model->id) {
            return false;
        }

        return true;
    }

    /**
     * Determine whether the user can toggle the status.
     */
    public function toggleStatus(User $user, User $model): bool
    {
        // Can't toggle your own status
        if ($user->id === $model->id) {
            return false;
        }

        // Admin can toggle anyone except themselves
        if ($user->isAdmin()) {
            return true;
        }

        // Supervisor can toggle non-admin users
        if ($user->isSupervisor()) {
            return !$model->isAdmin();
        }

        return false;
    }
}
