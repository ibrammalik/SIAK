<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\Marker;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class MarkerPolicy
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
    public function view(User $user, Marker $marker): bool
    {
        return in_array($user->role, [
            UserRole::SuperAdmin,
            UserRole::AdminKelurahan,
            // UserRole::KetuaRW,
            // UserRole::KetuaRT,
        ]);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return in_array($user->role, [
            UserRole::SuperAdmin,
            UserRole::AdminKelurahan,
            // UserRole::KetuaRW,
            // UserRole::KetuaRT,
        ]);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Marker $marker): bool
    {
        return in_array($user->role, [
            UserRole::SuperAdmin,
            UserRole::AdminKelurahan,
            // UserRole::KetuaRW,
            // UserRole::KetuaRT,
        ]);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Marker $marker): bool
    {
        return in_array($user->role, [
            UserRole::SuperAdmin,
            UserRole::AdminKelurahan,
            // UserRole::KetuaRW,
            // UserRole::KetuaRT,
        ]);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Marker $marker): bool
    {
        return in_array($user->role, [
            UserRole::SuperAdmin,
            UserRole::AdminKelurahan,
            // UserRole::KetuaRW,
            // UserRole::KetuaRT,
        ]);
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Marker $marker): bool
    {
        return in_array($user->role, [
            UserRole::SuperAdmin,
            UserRole::AdminKelurahan,
            // UserRole::KetuaRW,
            // UserRole::KetuaRT,
        ]);
    }
}
