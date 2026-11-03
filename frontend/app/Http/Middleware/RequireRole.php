<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RequireRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  mixed  ...$roles
     * @return \Symfony\Component\HttpFoundation\Response
     */
     public function handle(Request $request, Closure $next, ...$roles): Response
    {
        $role = strtoupper((string) session('auth_role'));

        if (!$role) {
            return redirect()->route('login')->withErrors([
                'username' => 'Silakan login terlebih dahulu.'
            ]);
        }

        $allowed = array_map(fn ($r) => strtoupper((string) $r), $roles);

        if (!in_array($role, $allowed, true)) {
            abort(403, 'Anda tidak memiliki akses ke halaman ini.');
        }

        return $next($request);
    }
}
