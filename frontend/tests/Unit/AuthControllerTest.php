<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Http\Controllers\AuthController;
use App\Services\ExpressApiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;

class AuthControllerTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['services.express_api.url' => 'http://localhost:5000/api']);
    }

    public function test_show_login_view(): void
    {
        $this->withoutVite();
        $controller = new AuthController();
        $view = $controller->showLogin();
        $this->assertEquals('auth.login', $view->name());
    }

    public function test_show_register_view(): void
    {
        $this->withoutVite();
        $controller = new AuthController();
        $view = $controller->showRegister();
        $this->assertEquals('auth.register', $view->name());
    }

    public function test_login_success(): void
    {
        Http::fake([
            '*/auth/login' => Http::response([
                'token' => 'mock-jwt-token',
                'role' => 'PASIEN',
                'akun_id' => 1,
            ], 200),
            '*/auth/me' => Http::response([
                'user' => ['id' => 1],
                'profil' => ['id' => 99, 'nama' => 'Budi Santoso']
            ], 200),
        ]);

        $request = Request::create('/login', 'POST', [
            'username' => 'pasien1',
            'password' => 'Password123!',
        ]);
        $request->setLaravelSession(app('session')->driver());

        $controller = new AuthController();
        $api = new ExpressApiService();

        $response = $controller->login($request, $api);

        $this->assertEquals(302, $response->getStatusCode());
        $this->assertEquals('mock-jwt-token', session('api_token'));
        $this->assertEquals('PASIEN', session('auth_role'));
        $this->assertEquals(99, session('pasien_id'));
    }

    public function test_login_failure(): void
    {
        Http::fake([
            '*/auth/login' => Http::response([
                'message' => 'Invalid credentials',
            ], 401),
        ]);

        $request = Request::create('/login', 'POST', [
            'username' => 'wronguser',
            'password' => 'Wrongpass123',
        ]);
        $request->setLaravelSession(app('session')->driver());

        $controller = new AuthController();
        $api = new ExpressApiService();

        $response = $controller->login($request, $api);

        $this->assertEquals(302, $response->getStatusCode());
        $this->assertNull(session('api_token'));
    }

    public function test_logout(): void
    {
        $request = Request::create('/logout', 'POST');
        $request->setLaravelSession(app('session')->driver());
        session(['api_token' => 'active-token']);

        $controller = new AuthController();
        $response = $controller->logout($request);

        $this->assertEquals(302, $response->getStatusCode());
        $this->assertNull(session('api_token'));
    }
}
