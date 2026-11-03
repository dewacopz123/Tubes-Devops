<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RequireApiLogin
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!session()->has('api_token') || !session('api_token')) {
            return redirect()->route('login')->withErrors([
                'username' => 'Silakan login terlebih dahulu.'
            ]);
        }

        return $next($request);
    }
}
