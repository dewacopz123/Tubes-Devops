<?php

namespace App\Services;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\UploadedFile;

class ExpressApiService
{
    private string $baseUrl;

    public function __construct()
    {
        $this->baseUrl = rtrim(config('services.express_api.url'), '/');
    }

    /**
     * Create HTTP client with proper headers
     * 
     * @param bool $auth Whether to include Authorization header
     * @param bool $isMultipart Whether this is a multipart/form-data request
     * @return PendingRequest
     */
    private function client(bool $auth = true, bool $isMultipart = false): PendingRequest
    {
        $headers = [
            'Accept' => 'application/json',
        ];

        // Only add Content-Type for non-multipart requests
        // Multipart requests will set their own Content-Type with boundary
        if (!$isMultipart) {
            $headers['Content-Type'] = 'application/json';
        }

        if ($auth && session()->has('api_token')) {
            $headers['Authorization'] = 'Bearer ' . session('api_token');
        }

        return Http::withHeaders($headers);
    }

    /**
     * Build full URL from path
     */
    private function url(string $path): string
    {
        $path = '/' . ltrim($path, '/');
        return "{$this->baseUrl}{$path}";
    }

    /**
     * Log API error for debugging
     * 
     * @param string $method HTTP method
     * @param string $endpoint API endpoint
     * @param Response $response API response
     * @param array $context Additional context
     */
    private function logApiError(string $method, string $endpoint, Response $response, array $context = []): void
    {
        $status = $response->status();
        $message = $response->json('message') ?? 'Unknown error';
        
        \Log::error("API Error: {$method} {$endpoint}", [
            'status' => $status,
            'message' => $message,
            'endpoint' => $endpoint,
            'method' => $method,
            'response_body' => $response->body(),
            'context' => $context,
        ]);
    }

    /**
     * Get user-friendly error message based on status code
     * 
     * @param Response $response
     * @return string
     */
    public function getUserFriendlyError(Response $response): string
    {
        $status = $response->status();
        $apiMessage = $response->json('message');

        return match($status) {
            401 => 'Sesi Anda telah berakhir. Silakan login kembali.',
            403 => 'Anda tidak memiliki akses untuk melakukan tindakan ini.',
            404 => $apiMessage ?? 'Data yang Anda cari tidak ditemukan.',
            409 => $apiMessage ?? 'Data sudah ada. Silakan gunakan data yang berbeda.',
            422 => $apiMessage ?? 'Data yang Anda masukkan tidak valid.',
            429 => 'Terlalu banyak percobaan. Silakan tunggu beberapa saat dan coba lagi.',
            500 => 'Terjadi kesalahan pada server. Silakan coba lagi atau hubungi administrator.',
            502, 503 => 'Layanan sedang tidak tersedia. Silakan coba lagi nanti.',
            default => $apiMessage ?? 'Terjadi kesalahan. Silakan coba lagi.',
        };
    }

    /**
     * Check if response indicates session expired (401)
     * 
     * @param Response $response
     * @return bool
     */
    public function isSessionExpired(Response $response): bool
    {
        return $response->status() === 401;
    }

    /**
     * Check if response indicates rate limit (429)
     * 
     * @param Response $response
     * @return bool
     */
    public function isRateLimited(Response $response): bool
    {
        return $response->status() === 429;
    }


    // ========================================
    // AUTH MODULE
    // ========================================

    /**
     * Login user
     * POST /auth/login
     */
    public function login(string $username, string $password): Response
    {
        return $this->client(false)->post($this->url('/auth/login'), [
            'username' => $username,
            'password' => $password,
        ]);
    }

    /**
     * Register pasien
     * POST /auth/register
     */
    public function registerPasien(array $payload): Response
    {
        return $this->client(false)->post($this->url('/auth/register'), $payload);
    }

    /**
     * Register dokter
     * POST /auth/register-dokter
     */
    public function registerDokter(array $payload): Response
    {
        return $this->client(false)->post($this->url('/auth/register-dokter'), $payload);
    }

    /**
     * Register petugas
     * POST /auth/register-petugas
     */
    public function registerPetugas(array $payload): Response
    {
        return $this->client(false)->post($this->url('/auth/register-petugas'), $payload);
    }

    /**
     * Get current user profile
     * GET /auth/me
     */
    public function authMe(): Response
    {
        return $this->client(true)->get($this->url('/auth/me'));
    }

    // ========================================
    // REGISTRATION MODULE
    // ========================================

    /**
     * Create new registration (pendaftaran)
     * POST /registrations/
     * 
     * @param array $payload
     * @param UploadedFile|null $suratRujukan
     * @return Response
     */
    public function registrationCreate(array $payload, ?UploadedFile $suratRujukan): Response
    {
        if ($suratRujukan === null) {
            // If no file, send as JSON
            return $this->client(true)->post($this->url('/registrations/'), $payload);
        }

        // With file, use multipart
        return $this->client(true, true)
            ->attach(
                'surat_rujukan',
                file_get_contents($suratRujukan->getRealPath()),
                $suratRujukan->getClientOriginalName()
            )
            ->post($this->url('/registrations/'), $payload);
    }

    /**
     * Get my registrations
     * GET /registrations/me
     */
    public function registrationMe(array $query = []): Response
    {
        return $this->client(true)->get($this->url('/registrations/me'), $query);
    }

    /**
     * Get my queue today
     * GET /registrations/queue/today
     */
    public function registrationQueueToday(): Response
    {
        return $this->client(true)->get($this->url('/registrations/queue/today'));
    }

    /**
     * Download surat rujukan
     * GET /registrations/:id/surat-rujukan/download
     */
    public function downloadSuratRujukan(int $id): Response
    {
        return $this->client(true)->get($this->url("/registrations/{$id}/surat-rujukan/download"));
    }

    // ========================================
    // PATIENT MODULE
    // ========================================

    /**
     * Get list of patients
     * GET /patients/
     */
    public function pasien(array $params = []): Response
    {
        return $this->client(true)->get($this->url('/patients/'), $params);
    }

    /**
     * Get patient detail
     * GET /patients/:id
     */
    public function pasienDetail(int $id): Response
    {
        return $this->client(true)->get($this->url("/patients/{$id}"));
    }

    /**
     * Advanced patient search
     * POST /patients/search
     */
    public function patientSearch(array $criteria): Response
    {
        return $this->client(true)->post($this->url('/patients/search'), $criteria);
    }

    // ========================================
    // QUEUE MODULE
    // ========================================

    /**
     * Get queue today
     * GET /queue/today
     */
    public function queueToday(array $query = []): Response
    {
        return $this->client(true)->get($this->url('/queue/today'), $query);
    }

    /**
     * Get queue statistics
     * GET /queue/stats
     */
    public function queueStats(): Response
    {
        return $this->client(true)->get($this->url('/queue/stats'));
    }

    /**
     * Call queue
     * POST /queue/:id/call
     */
    public function queueCall(int $id): Response
    {
        return $this->client(true)->post($this->url("/queue/{$id}/call"));
    }

    /**
     * Next queue
     * POST /queue/:id/next
     */
    public function queueNext(int $id): Response
    {
        return $this->client(true)->post($this->url("/queue/{$id}/next"));
    }

    /**
     * Cancel queue
     * POST /queue/:id/cancel
     */
    public function queueCancel(int $id, ?string $reason = null): Response
    {
        $payload = [];
        if ($reason !== null) {
            $payload['reason'] = $reason;
        }

        return $this->client(true)->post($this->url("/queue/{$id}/cancel"), $payload);
    }

    // ========================================
    // EXAM MODULE
    // ========================================

    /**
     * Get all exams
     * GET /exams/all
     */
    public function examsList(array $params = []): Response
    {
        return $this->client(true)->get($this->url('/exams/all'), $params);
    }

    /**
     * Get exams by patient
     * GET /exams/patients/:pasienId
     */
    public function examsByPatient(int $pasienId): Response
    {
        return $this->client(true)->get($this->url("/exams/patients/{$pasienId}"));
    }

    /**
     * Get exam detail
     * GET /exams/:id
     */
    public function examDetail(int $id): Response
    {
        return $this->client(true)->get($this->url("/exams/{$id}"));
    }

    /**
     * Create exam
     * POST /exams/
     */
    public function createExam(array $payload): Response
    {
        return $this->client(true)->post($this->url('/exams/'), $payload);
    }

    /**
     * Update exam
     * PATCH /exams/:id
     */
    public function patchExam(int $id, array $patch): Response
    {
        return $this->client(true)->patch($this->url("/exams/{$id}"), $patch);
    }

    /**
     * Upload exam file
     * POST /exams/:id/files
     */
    public function uploadExamFile(int $id, UploadedFile $file): Response
    {
        return $this->client(true, true)
            ->attach(
                'file',
                file_get_contents($file->getRealPath()),
                $file->getClientOriginalName()
            )
            ->post($this->url("/exams/{$id}/files"));
    }

    /**
     * Replace exam file
     * PATCH /exams/:id/files/:fileId
     */
    public function replaceExamFile(int $examId, int $fileId, UploadedFile $file): Response
    {
        return $this->client(true, true)
            ->attach(
                'file',
                file_get_contents($file->getRealPath()),
                $file->getClientOriginalName()
            )
            ->patch($this->url("/exams/{$examId}/files/{$fileId}"));
    }

    /**
     * Download exam file
     * GET /exams/:id/files/:fileId/download
     */
    public function downloadExamFile(int $examId, int $fileId): Response
    {
        return $this->client(true)->get($this->url("/exams/{$examId}/files/{$fileId}/download"));
    }

    /**
     * Delete exam
     * DELETE /exams/:id
     */
    public function deleteExam(int $id): Response
    {
        return $this->client(true)->delete($this->url("/exams/{$id}"));
    }

    // ========================================
    // AUDIT LOG MODULE
    // ========================================

    /**
     * Get audit logs
     * GET /audit-logs
     */
    public function auditLogs(array $params = []): Response
    {
        return $this->client(true)->get($this->url('/audit-logs'), $params);
    }

    // ========================================
    // DEVICE MODULE
    // ========================================

    /**
     * Save device token for push notifications
     * POST /devices/token
     */
    public function saveDeviceToken(string $fcmToken, string $platform = 'ANDROID'): Response
    {
        return $this->client(true)->post($this->url('/devices/token'), [
            'fcm_token' => $fcmToken,
            'platform' => $platform,
        ]);
    }

    // ========================================
    // DEPRECATED METHODS (for backward compatibility)
    // These will be removed in future versions
    // ========================================

    /**
     * @deprecated Use examsByPatient() instead
     */
    public function listByPatient(int $pasienId): Response
    {
        return $this->examsByPatient($pasienId);
    }

    /**
     * @deprecated Use examDetail() instead
     */
    public function detail(int $id): Response
    {
        return $this->examDetail($id);
    }

    /**
     * Generic GET request (for backward compatibility)
     * @deprecated Use specific methods instead
     */
    public function get(string $path, array $query = []): Response
    {
        return $this->client(true)->get($this->url($path), $query);
    }
}
