<?php

namespace App\Policies;

use App\Models\Harvest_planning;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class HarvestPlanningPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasRole('Admin,SuperAdmin,Registrador');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Harvest_planning $harvestPlanning): bool
    {
        return $user->hasRole('Admin,SuperAdmin,Registrador');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasRole('Admin,SuperAdmin,Registrador');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Harvest_planning $harvestPlanning): bool
    {
        return $user->hasRole('Admin,SuperAdmin,Registrador');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Harvest_planning $harvestPlanning): bool
    {
        return $user->hasRole('Admin,SuperAdmin,Registrador');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Harvest_planning $harvestPlanning): bool
    {
        return $user->hasRole('Admin,SuperAdmin,Registrador');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Harvest_planning $harvestPlanning): bool
    {
        return $user->hasRole('Admin,SuperAdmin,Registrador');
    }
}
