<?php

namespace App\Http\Controllers\ClientPortal;

use App\Http\Controllers\Controller;
use App\Models\ClientUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Show the client portal login form.
     */
    public function showLoginForm()
    {
        return inertia('ClientPortal/Auth/Login');
    }

    /**
     * Handle client portal login.
     */
    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        $clientUser = ClientUser::where('email', $request->email)
            ->where('is_active', true)
            ->first();

        if (!$clientUser || !Hash::check($request->password, $clientUser->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        Auth::guard('client')->login($clientUser, $request->boolean('remember'));

        $request->session()->regenerate();

        return redirect()->intended(route('client-portal.dashboard'));
    }

    /**
     * Handle client portal logout.
     */
    public function logout(Request $request)
    {
        Auth::guard('client')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('client-portal.login');
    }
}
