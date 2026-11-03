<?php

namespace App\Http\Controllers;

use App\Services\ExpressApiService;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request, ExpressApiService $api)
    {
        // Validasi input - password min 6 untuk allow existing users
        $data = $request->validate([
            'username' => ['required', 'string', 'max:50'],
            'password' => ['required', 'string', 'min:6'],
        ]);

        $resp = $api->login($data['username'], $data['password']);

        if (!$resp->successful()) {
            $message = $resp->json('message') ?? 'Username atau password salah';
            return back()
                ->withErrors(['username' => $message])
                ->withInput();
        }

        $json = $resp->json();

        // Simpan token dan role ke session
        session([
            'api_token' => $json['token'] ?? null,
            'auth_role' => $json['role'] ?? null,
            'auth_username' => $data['username'],
        ]);

        // Call /auth/me untuk mendapatkan profile lengkap dan simpan user_id
        $this->saveUserProfile($api);

        $role = strtoupper((string) session('auth_role'));

        $map = [
            'PASIEN'  => '/dashboard-pasien',
            'DOKTER'  => '/dashboard-dokter',
            'PETUGAS' => '/dashboard-petugas',
        ];

        $routeName = $map[$role] ?? 'login';

        $request->session()->regenerate();

        return redirect()->to($routeName)->with('success', 'Login berhasil! Selamat datang kembali.');
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request, ExpressApiService $api)
    {
        // Validasi dengan password ketat untuk user baru
        $data = $request->validate([
            'username' => ['required', 'string', 'max:50'],
            'email' => ['required', 'email', 'max:120'],
            'password' => [
                'required',
                'string',
                'min:8',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&#])[A-Za-z\d@$!%*?&#]{8,}$/'
            ],
            'nik' => ['required', 'string', 'size:16'],
            'nama' => ['required', 'string', 'max:100'],
            'jenis_kelamin' => ['required', 'in:L,P'],
            'tgl_lahir' => ['nullable', 'date'],
            'alamat' => ['nullable', 'string', 'max:255'],
            'no_telepon' => ['nullable', 'string', 'max:20'],
        ], [
            'password.min' => 'Password harus minimal 8 karakter',
            'password.regex' => 'Password harus mengandung huruf besar, huruf kecil, angka, dan karakter khusus (@$!%*?&#)',
            'username.required' => 'Username wajib diisi',
            'email.required' => 'Email wajib diisi',
            'email.email' => 'Format email tidak valid',
            'nik.required' => 'NIK wajib diisi',
            'nik.size' => 'NIK harus 16 digit',
            'nama.required' => 'Nama lengkap wajib diisi',
            'jenis_kelamin.required' => 'Jenis kelamin wajib dipilih',
        ]);

        // API menerima tgl_lahir format YYYY-MM-DD
        if (!empty($data['tgl_lahir'])) {
            $data['tgl_lahir'] = date('Y-m-d', strtotime($data['tgl_lahir']));
        } else {
            $data['tgl_lahir'] = null;
        }

        $resp = $api->registerPasien($data);

        if (!$resp->successful()) {
            $message = $resp->json('message') ?? 'Pendaftaran gagal. Silakan coba lagi.';
            return back()
                ->withErrors(['username' => $message])
                ->withInput();
        }

        $json = $resp->json();

        // Simpan token dan role ke session - gunakan akun_id bukan id
        session([
            'api_token' => $json['token'] ?? null,
            'auth_role' => $json['role'] ?? 'PASIEN',
            'auth_username' => $data['username'],
            'auth_akun_id' => $json['akun_id'] ?? null,
        ]);

        // Call /auth/me untuk mendapatkan profile lengkap dan simpan pasien_id
        $this->saveUserProfile($api);

        $request->session()->regenerate();

        return redirect()->route('pasien.dashboard')->with('success', 'Pendaftaran berhasil! Selamat datang di eLabora.');
    }


    public function logout(Request $request)
    {
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login')->with('success', 'Anda telah berhasil logout.');
    }

    /**
     * Helper method untuk save user profile ke session
     * Call /auth/me dan simpan pasien_id/dokter_id/petugas_id
     */
    private function saveUserProfile(ExpressApiService $api)
    {
        $meResp = $api->authMe();

        if ($meResp->successful()) {
            $meData = $meResp->json();
            $profil = $meData['profil'] ?? [];
            $role = strtoupper((string) session('auth_role'));

            // Simpan user_id sesuai role
            if ($role === 'PASIEN' && isset($profil['id'])) {
                session(['pasien_id' => $profil['id']]);
            } elseif ($role === 'DOKTER' && isset($profil['id'])) {
                session(['dokter_id' => $profil['id']]);
            } elseif ($role === 'PETUGAS' && isset($profil['id'])) {
                session(['petugas_id' => $profil['id']]);
            }

            // Simpan nama untuk display
            if (isset($profil['nama'])) {
                session(['auth_name' => $profil['nama']]);
            }
        }
    }
}
