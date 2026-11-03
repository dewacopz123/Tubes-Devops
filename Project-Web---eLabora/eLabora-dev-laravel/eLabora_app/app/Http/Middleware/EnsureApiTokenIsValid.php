<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureApiTokenIsValid
{
    /**
     * Handle an incoming request.
     *
     * Middleware ini memastikan user memiliki api_token yang valid di session
     * sebelum mengakses halaman yang memerlukan autentikasi.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if api_token exists in session
        if (!session()->has('api_token')) {
            // Redirect to login with message
            return redirect()
                ->route('login')
                ->with('error', 'Sesi Anda telah berakhir. Silakan login kembali.');
        }

        // Check if token is not empty
        $token = session('api_token');
        if (empty($token)) {
            session()->flush();
            return redirect()
                ->route('login')
                ->with('error', 'Sesi Anda tidak valid. Silakan login kembali.');
        }

        return $next($request);
    }
}
