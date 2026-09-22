<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;

class CsrfTokenController extends Controller
{
    /**
     * Return the current CSRF token so a long-lived page can keep it fresh.
     *
     * Requesting this route also counts as session activity, keeping the
     * session (and therefore its token) from expiring while the app is open.
     */
    public function show(): JsonResponse
    {
        return response()->json([
            'token' => csrf_token(),
        ]);
    }
}
