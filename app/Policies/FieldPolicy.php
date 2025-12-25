<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Farms\Farm;
use App\Models\Farms\Field;
use App\Models\Farmers\Farmer;
use App\Enums\UserRole;
use Illuminate\Auth\Access\HandlesAuthorization;

class FieldPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any fields.
     */
    public function viewAny(User $user, Farm $farm): bool
    {
        // Check if user can view the parent farm
        return $user->can('view', $farm);
    }

    /**
     * Determine whether the user can view the field.
     */
    public function view(User $user, Field $field): bool
    {
        // Get the farm through relationship
        $farm = $field->farm;
        
        // Check if user can view the parent farm
        return $user->can('view', $farm);
    }

    /**
     * Determine whether the user can create fields.
     */
    public function create(User $user, Farm $farm): bool
    {
        // Check if user can update the parent farm
        return $user->can('update', $farm);
    }

    /**
     * Determine whether the user can update the field.
     */
    public function update(User $user, Field $field): bool
    {
        // Get the farm through relationship
        $farm = $field->farm;
        
        // Check if user can update the parent farm
        return $user->can('update', $farm);
    }

    /**
     * Determine whether the user can delete the field.
     */
    public function delete(User $user, Field $field): bool
    {
        // Get the farm through relationship
        $farm = $field->farm;
        
        // Only admins and supervisors can delete fields
        if ($user->hasRole(UserRole::ADMIN) || $user->hasRole(UserRole::SUPERVISOR)) {
            return $user->can('update', $farm);
        }
        
        return false;
    }

    /**
     * Determine whether the user can restore the field.
     */
    public function restore(User $user, Field $field): bool
    {
        // Only admins can restore deleted fields
        return $user->hasRole(UserRole::ADMIN);
    }

    /**
     * Determine whether the user can permanently delete the field.
     */
    public function forceDelete(User $user, Field $field): bool
    {
        // Only admins can permanently delete fields
        return $user->hasRole(UserRole::ADMIN);
    }

    /**
     * Determine whether the user can manage field boundaries.
     */
    public function manageBoundaries(User $user, Field $field): bool
    {
        return $this->update($user, $field);
    }

    /**
     * Determine whether the user can view field history.
     */
    public function viewHistory(User $user, Field $field): bool
    {
        return $this->view($user, $field);
    }

    /**
     * Determine whether the user can update field status.
     */
    public function updateStatus(User $user, Field $field): bool
    {
        return $this->update($user, $field);
    }

    /**
     * Determine whether the user can record harvest.
     */
    public function recordHarvest(User $user, Field $field): bool
    {
        return $this->update($user, $field);
    }

    /**
     * Determine whether the user can view field map.
     */
    public function viewMap(User $user, Field $field): bool
    {
        return $this->view($user, $field);
    }
}