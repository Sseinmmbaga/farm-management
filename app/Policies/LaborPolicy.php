<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Tasks\Labor;
use App\Models\Farmers\Farmer;
use App\Enums\UserRole;
use Illuminate\Auth\Access\HandlesAuthorization;

class LaborPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any labor records.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole([
            UserRole::ADMIN,
            UserRole::SUPERVISOR,
            UserRole::EXTENSION_OFFICER,
            UserRole::PRODUCTION_MANAGER,
            UserRole::ACCOUNTANT,
            UserRole::FARMER
        ]);
    }

    /**
     * Determine whether the user can view the labor record.
     */
    public function view(User $user, Labor $labor): bool
    {
        // Admins, supervisors, production managers, and accountants can view all records
        if ($user->hasAnyRole([
            UserRole::ADMIN,
            UserRole::SUPERVISOR,
            UserRole::PRODUCTION_MANAGER,
            UserRole::ACCOUNTANT
        ])) {
            return true;
        }

        // Extension officers can view labor for farmers they supervise
        if ($user->hasRole(UserRole::EXTENSION_OFFICER)) {
            // They created the record
            if ($labor->created_by === $user->id) {
                return true;
            }

            // It's for a task they can view
            if ($labor->task && $labor->task->created_by === $user->id) {
                return true;
            }

            // It's for a farm of a farmer they supervise
            if ($labor->farm_id) {
                $assignedFarmerIds = Farmer::where('extension_officer_id', $user->id)->pluck('id')->toArray();
                $farm = $labor->farm;
                if ($farm && in_array($farm->farmer_id, $assignedFarmerIds)) {
                    return true;
                }
            }

            return false;
        }

        // Farmers can view their own labor records
        if ($user->hasRole(UserRole::FARMER) && $user->farmer) {
            // Worker is the farmer
            if ($labor->worker_type === Farmer::class && $labor->worker_id === $user->farmer->id) {
                return true;
            }

            // Worker is the user
            if ($labor->worker_type === User::class && $labor->worker_id === $user->id) {
                return true;
            }

            // Labor is for their farm
            if ($labor->farm_id && $labor->farm && $labor->farm->farmer_id === $user->farmer->id) {
                return true;
            }

            return false;
        }

        return false;
    }

    /**
     * Determine whether the user can create labor records.
     */
    public function create(User $user): bool
    {
        return $user->hasAnyRole([
            UserRole::ADMIN,
            UserRole::SUPERVISOR,
            UserRole::EXTENSION_OFFICER,
            UserRole::PRODUCTION_MANAGER,
            UserRole::FARMER
        ]);
    }

    /**
     * Determine whether the user can update the labor record.
     */
    public function update(User $user, Labor $labor): bool
    {
        // Cannot update paid records
        if ($labor->isPaid()) {
            return $user->hasRole(UserRole::ADMIN);
        }

        // Admins can update all records
        if ($user->hasRole(UserRole::ADMIN)) {
            return true;
        }

        // Supervisors and production managers can update all unpaid records
        if ($user->hasAnyRole([UserRole::SUPERVISOR, UserRole::PRODUCTION_MANAGER])) {
            return true;
        }

        // Record creator can update if not verified/approved
        if ($labor->created_by === $user->id && !$labor->is_verified && $labor->isPending()) {
            return true;
        }

        // Extension officers can update records for their farmers
        if ($user->hasRole(UserRole::EXTENSION_OFFICER)) {
            if ($labor->farm_id) {
                $assignedFarmerIds = Farmer::where('extension_officer_id', $user->id)->pluck('id')->toArray();
                $farm = $labor->farm;
                if ($farm && in_array($farm->farmer_id, $assignedFarmerIds)) {
                    return !$labor->is_verified && $labor->isPending();
                }
            }
        }

        // Farmers can update their own unverified records
        if ($user->hasRole(UserRole::FARMER) && $user->farmer) {
            if ($labor->created_by === $user->id && !$labor->is_verified && $labor->isPending()) {
                return true;
            }
        }

        return false;
    }

    /**
     * Determine whether the user can delete the labor record.
     */
    public function delete(User $user, Labor $labor): bool
    {
        // Cannot delete paid records
        if ($labor->isPaid()) {
            return $user->hasRole(UserRole::ADMIN);
        }

        // Admins and supervisors can delete
        if ($user->hasAnyRole([UserRole::ADMIN, UserRole::SUPERVISOR])) {
            return true;
        }

        // Creator can delete if pending and unverified
        if ($labor->created_by === $user->id && !$labor->is_verified && $labor->isPending()) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can restore the labor record.
     */
    public function restore(User $user, Labor $labor): bool
    {
        return $user->hasAnyRole([UserRole::ADMIN, UserRole::SUPERVISOR]);
    }

    /**
     * Determine whether the user can permanently delete the labor record.
     */
    public function forceDelete(User $user, Labor $labor): bool
    {
        return $user->hasRole(UserRole::ADMIN);
    }

    /**
     * Determine whether the user can verify labor records.
     */
    public function verify(User $user, Labor $labor): bool
    {
        if ($labor->is_verified) {
            return false;
        }

        return $user->hasAnyRole([
            UserRole::ADMIN,
            UserRole::SUPERVISOR,
            UserRole::PRODUCTION_MANAGER
        ]);
    }

    /**
     * Determine whether the user can approve labor records for payment.
     */
    public function approve(User $user, Labor $labor): bool
    {
        if (!$labor->isPending()) {
            return false;
        }

        return $user->hasAnyRole([
            UserRole::ADMIN,
            UserRole::SUPERVISOR,
            UserRole::PRODUCTION_MANAGER,
            UserRole::ACCOUNTANT
        ]);
    }

    /**
     * Determine whether the user can mark labor as paid.
     */
    public function markAsPaid(User $user, Labor $labor): bool
    {
        if ($labor->isPaid()) {
            return false;
        }

        return $user->hasAnyRole([
            UserRole::ADMIN,
            UserRole::ACCOUNTANT
        ]);
    }

    /**
     * Determine whether the user can export labor records.
     */
    public function export(User $user): bool
    {
        return $user->hasAnyRole([
            UserRole::ADMIN,
            UserRole::SUPERVISOR,
            UserRole::PRODUCTION_MANAGER,
            UserRole::ACCOUNTANT
        ]);
    }
}
