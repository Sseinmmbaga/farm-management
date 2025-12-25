<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Tasks\Task;
use App\Models\Farmers\Farmer;
use App\Enums\UserRole;
use Illuminate\Auth\Access\HandlesAuthorization;

class TaskPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any tasks.
     */
    public function viewAny(User $user): bool
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
     * Determine whether the user can view the task.
     */
    public function view(User $user, Task $task): bool
    {
        // Admins, supervisors, and production managers can view all tasks
        if ($user->hasAnyRole([UserRole::ADMIN, UserRole::SUPERVISOR, UserRole::PRODUCTION_MANAGER])) {
            return true;
        }

        // Extension officers can view tasks they created or are assigned to them
        if ($user->hasRole(UserRole::EXTENSION_OFFICER)) {
            // Can view if they created the task
            if ($task->created_by === $user->id) {
                return true;
            }

            // Can view if assigned to them
            if ($task->assignments()->forUser($user->id)->exists()) {
                return true;
            }

            // Can view if it's for a farmer they supervise
            $assignedFarmerIds = Farmer::where('extension_officer_id', $user->id)->pluck('id')->toArray();
            if ($task->farmer_id && in_array($task->farmer_id, $assignedFarmerIds)) {
                return true;
            }

            return false;
        }

        // Farmers can view tasks assigned to them or for their farm/field
        if ($user->hasRole(UserRole::FARMER) && $user->farmer) {
            // Task is for their farm
            if ($task->farmer_id === $user->farmer->id) {
                return true;
            }

            // Task is assigned to them
            if ($task->assignments()->forUser($user->id)->exists()) {
                return true;
            }

            return false;
        }

        return false;
    }

    /**
     * Determine whether the user can create tasks.
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
     * Determine whether the user can update the task.
     */
    public function update(User $user, Task $task): bool
    {
        // Admins and supervisors can update all tasks
        if ($user->hasAnyRole([UserRole::ADMIN, UserRole::SUPERVISOR])) {
            return true;
        }

        // Production managers can update all tasks
        if ($user->hasRole(UserRole::PRODUCTION_MANAGER)) {
            return true;
        }

        // Extension officers can update tasks they created
        if ($user->hasRole(UserRole::EXTENSION_OFFICER)) {
            if ($task->created_by === $user->id) {
                return true;
            }

            // Can update tasks for farmers they supervise
            $assignedFarmerIds = Farmer::where('extension_officer_id', $user->id)->pluck('id')->toArray();
            if ($task->farmer_id && in_array($task->farmer_id, $assignedFarmerIds)) {
                return true;
            }

            return false;
        }

        // Farmers can update their own tasks (tasks for their farm)
        if ($user->hasRole(UserRole::FARMER) && $user->farmer) {
            if ($task->farmer_id === $user->farmer->id) {
                return true;
            }
        }

        return false;
    }

    /**
     * Determine whether the user can delete the task.
     */
    public function delete(User $user, Task $task): bool
    {
        // Only admins and supervisors can delete tasks
        if ($user->hasAnyRole([UserRole::ADMIN, UserRole::SUPERVISOR])) {
            return true;
        }

        // Production managers can delete tasks
        if ($user->hasRole(UserRole::PRODUCTION_MANAGER)) {
            return true;
        }

        // Task creator can delete if task is still pending
        if ($task->created_by === $user->id && $task->isPending()) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can restore the task.
     */
    public function restore(User $user, Task $task): bool
    {
        return $user->hasAnyRole([UserRole::ADMIN, UserRole::SUPERVISOR]);
    }

    /**
     * Determine whether the user can permanently delete the task.
     */
    public function forceDelete(User $user, Task $task): bool
    {
        return $user->hasRole(UserRole::ADMIN);
    }

    /**
     * Determine whether the user can assign tasks.
     */
    public function assign(User $user, Task $task): bool
    {
        // Same rules as update
        return $this->update($user, $task);
    }

    /**
     * Determine whether the user can complete the task.
     */
    public function complete(User $user, Task $task): bool
    {
        // Task must be in progress or on hold
        if (!$task->canBeCompleted()) {
            return false;
        }

        // Admins and supervisors can complete any task
        if ($user->hasAnyRole([UserRole::ADMIN, UserRole::SUPERVISOR, UserRole::PRODUCTION_MANAGER])) {
            return true;
        }

        // Task creator can complete
        if ($task->created_by === $user->id) {
            return true;
        }

        // Assigned users can complete
        if ($task->assignments()->forUser($user->id)->exists()) {
            return true;
        }

        // Farmers can complete tasks for their farm
        if ($user->hasRole(UserRole::FARMER) && $user->farmer) {
            if ($task->farmer_id === $user->farmer->id) {
                return true;
            }
        }

        return false;
    }

    /**
     * Determine whether the user can cancel the task.
     */
    public function cancel(User $user, Task $task): bool
    {
        // Task must be cancellable
        if (!$task->canBeCancelled()) {
            return false;
        }

        // Admins and supervisors can cancel any task
        if ($user->hasAnyRole([UserRole::ADMIN, UserRole::SUPERVISOR])) {
            return true;
        }

        // Production managers can cancel tasks
        if ($user->hasRole(UserRole::PRODUCTION_MANAGER)) {
            return true;
        }

        // Task creator can cancel if not yet started
        if ($task->created_by === $user->id && $task->isPending()) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can log labor for the task.
     */
    public function logLabor(User $user, Task $task): bool
    {
        // Anyone who can view the task can log labor for it
        return $this->view($user, $task);
    }
}
