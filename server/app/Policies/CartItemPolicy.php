<?php

namespace App\Policies;

use App\Models\Cart_item;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class CartItemPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Cart_item $cartItem): bool
    {
        // Admin vagy a kosár tulajdonosa tekintheti meg
        return $user->role === 1 || ($cartItem->cart && $user->id === $cartItem->cart->user_id);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Vásárlók tételt adhatnak a kosárhoz
        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Cart_item $cartItem): bool
    {
        // Admin vagy a kosár tulajdonosa módosíthatja
        return $user->role === 1 || ($cartItem->cart && $user->id === $cartItem->cart->user_id);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Cart_item $cartItem): bool
    {
        // Admin vagy a kosár tulajdonosa törölheti
        return $user->role === 1 || ($cartItem->cart && $user->id === $cartItem->cart->user_id);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Cart_item $cartItem): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Cart_item $cartItem): bool
    {
        return false;
    }
}
