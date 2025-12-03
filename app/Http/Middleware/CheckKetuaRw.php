<?php

namespace App\Http\Middleware;

use App\Enums\UserRole;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckKetuaRw
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if there is at least one user with role 'ketua_rw'
        $exists = User::where('role', UserRole::KetuaRW)->exists();

        if (!$exists) {
            // No ketua_rw user exists → redirect to onboarding page
            return redirect()->route('rw-onboarding'); // your Livewire onboarding page route
        }

        return $next($request);
    }
}
