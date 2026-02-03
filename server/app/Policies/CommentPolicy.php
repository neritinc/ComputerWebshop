<?php

namespace App\Policies;

use App\Models\Comment;
use App\Models\User;

class CommentPolicy
{
    /**
     * Anyone can view comments (public listing).
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Comment $comment): bool
    {
        return true;
    }

    /**
     * Customers (and admins) can create comments.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Admin or the author can update.
     */
    public function update(User $user, Comment $comment): bool
    {
        return $user->tokenCan('admin') || $user->id === $comment->user_id;
    }

    /**
     * Admin or the author can delete.
     */
    public function delete(User $user, Comment $comment): bool
    {
        return $user->role === 1 || $user->id === $comment->user_id;
    }

    public function restore(User $user, Comment $comment): bool
    {
        return false;
    }

    public function forceDelete(User $user, Comment $comment): bool
    {
        return false;
    }
}

