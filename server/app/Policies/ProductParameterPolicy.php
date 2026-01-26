<?php

namespace App\Policies;

use App\Models\ProductParameter;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ProductParameterPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // Mindenki megtekintheti a termék paramétereket
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, ProductParameter $productParameter): bool
    {
        // Mindenki megtekintheti az egyes termék paramétereket
        return true;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Csak adminok hozhatnak létre termék paramétereket
        return $user->role === 1;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, ProductParameter $productParameter): bool
    {
        // Csak adminok módosíthatnak termék paramétereket
        return $user->role === 1;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, ProductParameter $productParameter): bool
    {
        // Csak adminok törölhetnek termék paramétereket
        return $user->role === 1;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, ProductParameter $productParameter): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, ProductParameter $productParameter): bool
    {
        return false;
    }
}