<?php

namespace App\Policies;

use App\Models\Pic;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class PicPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // Mindenki megtekintheti a képeket
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Pic $pic): bool
    {
        // Mindenki megtekintheti az egyes képeket
        return true;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Csak adminok hozhatnak létre képeket
        return $user->role === 1;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Pic $pic): bool
    {
        // Csak adminok módosíthatnak képeket
        return $user->role === 1;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Pic $pic): bool
    {
        // Csak adminok törölhetnek képeket
        return $user->role === 1;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Pic $pic): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Pic $pic): bool
    {
        return false;
    }
}
