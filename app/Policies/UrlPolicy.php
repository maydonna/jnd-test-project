<?php

namespace App\Policies;

use App\Models\Url;
use App\Models\User;

class UrlPolicy
{
    /**
     * Determine whether the Clinic can update the model.
     */
    public function update(User $user, Url $model): bool
    {
        return $user->role === 'admin';
    }

    /**
     * Determine whether the Clinic can delete the model.
     */
    public function delete(User $user, Url $model): bool
    {
        return $user->id === $model->user_id || $user->role === 'admin';
    }
}
