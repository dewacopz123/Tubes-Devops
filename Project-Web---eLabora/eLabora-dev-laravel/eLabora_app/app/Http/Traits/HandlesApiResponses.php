<?php

namespace App\Http\Traits;

use App\Services\ExpressApiService;
use Illuminate\Http\Client\Response;
use Illuminate\Http\RedirectResponse;

trait HandlesApiResponses
{
    /**
     * Handle API response with consistent error handling
     * 
     * @param Response $response
     * @param ExpressApiService $api
     * @param string $successMessage
     * @param string|null $redirectRoute
     * @return RedirectResponse|null
     */
    protected function handleApiResponse(
        Response $response,
        ExpressApiService $api,
        string $successMessage = '',
        ?string $redirectRoute = null
    ): ?RedirectResponse {
        // Success case
        if ($response->successful()) {
            if ($successMessage && $redirectRoute) {
                return redirect()->route($redirectRoute)->with('success', $successMessage);
            }
            if ($successMessage) {
                return back()->with('success', $successMessage);
            }
            return null;
        }

        // Error case - handle 401 (session expired)
        if ($api->isSessionExpired($response)) {
            session()->flush();
            return redirect()
                ->route('login')
                ->with('error', 'Sesi Anda telah berakhir. Silakan login kembali.');
        }

        // Error case - get user-friendly message
        $errorMessage = $api->getUserFriendlyError($response);
        
        return back()
            ->withInput()
            ->with('error', $errorMessage);
    }

    /**
     * Handle API response for view rendering
     * Returns error message if failed, null if successful
     * 
     * @param Response $response
     * @param ExpressApiService $api
     * @return string|null
     */
    protected function getApiErrorForView(Response $response, ExpressApiService $api): ?string
    {
        if ($response->successful()) {
            return null;
        }

        // Handle 401 - session expired (will be handled by middleware on next request)
        if ($api->isSessionExpired($response)) {
            return 'Sesi Anda telah berakhir. Silakan login kembali.';
        }

        return $api->getUserFriendlyError($response);
    }
}
