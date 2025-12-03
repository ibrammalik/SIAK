<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\RW;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class RWPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return in_array($user->role, [
            UserRole::SuperAdmin,
            UserRole::AdminKelurahan,
            UserRole::KetuaRW,
        ]);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, RW $rw): bool
    {
        if ($user->isSuperAdmin() || $user->isKelurahan()) return true;
        if ($user->isRW() && $user->rw_id === $rw->id) return true;
        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        if ($user->isSuperAdmin() || $user->isKelurahan()) return true;
        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, RW $rW): bool
    {
        if ($user->isSuperAdmin() || $user->isKelurahan() || $user->isRW()) return true;
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, RW $rW): bool
    {
        if ($user->isSuperAdmin() || $user->isKelurahan()) return true;
        return false;
    }

    public function deleteAny(User $user): bool
    {
        if ($user->isSuperAdmin() || $user->isKelurahan()) return true;
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, RW $rW): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, RW $rW): bool
    {
        return false;
    }
}
