<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Farms\Farm;
use App\Models\Farmers\Farmer;
use App\Enums\UserRole;
use Illuminate\Auth\Access\HandlesAuthorization;

class FarmPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any farms.
     */
    public function viewAny(User $user): bool
    {
        // All authenticated users can view farms, but with different filters
        // ICS Inspectors have view-only access for inspection purposes
        return $user->hasAnyRole([
            UserRole::ADMIN,
            UserRole::SUPERVISOR,
            UserRole::EXTENSION_OFFICER,
            UserRole::FARMER,
            UserRole::ICS_INSPECTOR,
        ]);
    }

    /**
     * Determine whether the user can view the farm.
     */
    public function view(User $user, Farm $farm): bool
    {
        // Admins and supervisors can view all farms
        if ($user->hasRole(UserRole::ADMIN) || $user->hasRole(UserRole::SUPERVISOR)) {
            return true;
        }

        // ICS Inspectors can view all farms (view-only for inspection purposes)
        if ($user->hasRole(UserRole::ICS_INSPECTOR)) {
            return true;
        }

        // Extension officers can view farms of their assigned farmers
        if ($user->hasRole(UserRole::EXTENSION_OFFICER)) {
            $assignedFarmerIds = Farmer::where('extension_officer_id', $user->id)->pluck('id')->toArray();
            return in_array($farm->farmer_id, $assignedFarmerIds);
        }

        // Farmers can only view their own farms
        if ($user->hasRole(UserRole::FARMER)) {
            return $user->farmer && $user->farmer->id == $farm->farmer_id;
        }

        return false;
    }

    /**
     * Determine whether the user can create farms.
     */
    public function create(User $user): bool
    {
        // Admins, supervisors, extension officers, and farmers can create farms
        return $user->hasAnyRole([
            UserRole::ADMIN, 
            UserRole::SUPERVISOR, 
            UserRole::EXTENSION_OFFICER, 
            UserRole::FARMER
        ]);
    }

    /**
     * Determine whether the user can update the farm.
     */
    public function update(User $user, Farm $farm): bool
    {
        // Admins and supervisors can update all farms
        if ($user->hasRole(UserRole::ADMIN) || $user->hasRole(UserRole::SUPERVISOR)) {
            return true;
        }
        
        // Extension officers can update farms of their assigned farmers
        if ($user->hasRole(UserRole::EXTENSION_OFFICER)) {
            $assignedFarmerIds = Farmer::where('extension_officer_id', $user->id)->pluck('id')->toArray();
            return in_array($farm->farmer_id, $assignedFarmerIds);
        }
        
        // Farmers can only update their own farms
        if ($user->hasRole(UserRole::FARMER)) {
            return $user->farmer && $user->farmer->id == $farm->farmer_id;
        }
        
        return false;
    }

    /**
     * Determine whether the user can delete the farm.
     */
    public function delete(User $user, Farm $farm): bool
    {
        // Only admins and supervisors can delete farms
        return $user->hasRole(UserRole::ADMIN) || $user->hasRole(UserRole::SUPERVISOR);
    }

    /**
     * Determine whether the user can restore the farm.
     */
    public function restore(User $user, Farm $farm): bool
    {
        // Only admins can restore deleted farms
        return $user->hasRole(UserRole::ADMIN);
    }

    /**
     * Determine whether the user can permanently delete the farm.
     */
    public function forceDelete(User $user, Farm $farm): bool
    {
        // Only admins can permanently delete farms
        return $user->hasRole(UserRole::ADMIN);
    }

    /**
     * Determine whether the user can manage farm boundaries.
     */
    public function manageBoundaries(User $user, Farm $farm): bool
    {
        return $this->update($user, $farm);
    }

    /**
     * Determine whether the user can manage farm seasons.
     */
    public function manageSeasons(User $user, Farm $farm): bool
    {
        return $this->update($user, $farm);
    }

    /**
     * Determine whether the user can view farm history.
     */
    public function viewHistory(User $user, Farm $farm): bool
    {
        return $this->view($user, $farm);
    }
}
