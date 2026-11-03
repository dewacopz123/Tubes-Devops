<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use App\Http\Middleware\RequireRole;
use App\Http\Middleware\RequireApiLogin;
use App\Http\Middleware\EnsureApiTokenIsValid;

class MiddlewareTest extends TestCase
{
    public function test_require_role_redirects_when_no_role(): void
    {
        session()->forget('auth_role');
        $middleware = new RequireRole();
        $request = Request::create('/dokter/dashboard', 'GET');

        $response = $middleware->handle($request, function () {
            return new Response('OK');
        }, 'DOKTER');

        $this->assertEquals(302, $response->getStatusCode());
    }

    public function test_require_role_aborts_when_role_mismatch(): void
    {
        $this->expectException(HttpException::class);
        session(['auth_role' => 'PASIEN']);
        $middleware = new RequireRole();
        $request = Request::create('/dokter/dashboard', 'GET');

        $middleware->handle($request, function () {
            return new Response('OK');
        }, 'DOKTER');
    }

    public function test_require_role_passes_when_role_matches(): void
    {
        session(['auth_role' => 'DOKTER']);
        $middleware = new RequireRole();
        $request = Request::create('/dokter/dashboard', 'GET');

        $response = $middleware->handle($request, function () {
            return new Response('OK');
        }, 'DOKTER', 'PETUGAS_LAB');

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('OK', $response->getContent());
    }

    public function test_require_api_login_redirects_when_no_token(): void
    {
        session()->forget('api_token');
        $middleware = new RequireApiLogin();
        $request = Request::create('/dashboard', 'GET');

        $response = $middleware->handle($request, function () {
            return new Response('OK');
        });

        $this->assertEquals(302, $response->getStatusCode());
    }

    public function test_require_api_login_passes_when_token_present(): void
    {
        session(['api_token' => 'dummy-valid-token']);
        $middleware = new RequireApiLogin();
        $request = Request::create('/dashboard', 'GET');

        $response = $middleware->handle($request, function () {
            return new Response('OK');
        });

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('OK', $response->getContent());
    }

    public function test_ensure_api_token_is_valid_redirects_when_missing(): void
    {
        session()->forget('api_token');
        $middleware = new EnsureApiTokenIsValid();
        $request = Request::create('/dashboard', 'GET');

        $response = $middleware->handle($request, function () {
            return new Response('OK');
        });

        $this->assertEquals(302, $response->getStatusCode());
    }

    public function test_ensure_api_token_is_valid_redirects_when_empty(): void
    {
        session(['api_token' => '']);
        $middleware = new EnsureApiTokenIsValid();
        $request = Request::create('/dashboard', 'GET');

        $response = $middleware->handle($request, function () {
            return new Response('OK');
        });

        $this->assertEquals(302, $response->getStatusCode());
    }

    public function test_ensure_api_token_is_valid_passes_when_present(): void
    {
        session(['api_token' => 'sample-bearer-token']);
        $middleware = new EnsureApiTokenIsValid();
        $request = Request::create('/dashboard', 'GET');

        $response = $middleware->handle($request, function () {
            return new Response('OK');
        });

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('OK', $response->getContent());
    }
}

