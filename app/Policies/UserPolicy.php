<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class UserPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return in_array($user->role, [
            UserRole::SuperAdmin,
            UserRole::AdminKelurahan,
            // UserRole::KetuaRW,
            // UserRole::KetuaRT,
        ]);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, User $model): bool
    {
        if ($user->isSuperAdmin() || $user->isKelurahan()) {
            return true;
        }

        return $this->inSameScope($user, $model);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return in_array($user->role, [
            UserRole::SuperAdmin,
            UserRole::AdminKelurahan,
            UserRole::KetuaRW
        ]);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, User $model): bool
    {
        // Prevent editing themselves
        if ($user->id === $model->id && !$user->isSuperAdmin()) {
            return false;
        }

        if ($user->isSuperAdmin() || $user->isKelurahan()) {
            return true;
        }

        return $this->inSameScope($user, $model);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, User $model): bool
    {
        if ($user->role === UserRole::SuperAdmin) return true;
        return false;
    }

    public function deleteAny(User $user): bool
    {
        if ($user->role === UserRole::SuperAdmin) return true;
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, User $model): bool
    {
        return $user->role === UserRole::SuperAdmin;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, User $model): bool
    {
        return $user->role === UserRole::SuperAdmin;
    }

    /**
     * Role-aware scoping logic.
     * Works even if some fields are NULL.
     */
    protected function inSameScope(User $user, User $model): bool
    {
        // RW → compare rw only
        if ($user->isRW()) {
            return !is_null($user->rw_id) && $user->rw_id === $model->rw_id;
        }

        // RT → compare rt only (rw_id may be NULL)
        if ($user->isRT()) {
            return !is_null($user->rt_id) && $user->rt_id === $model->rt_id;
        }

        return false;
    }
}
