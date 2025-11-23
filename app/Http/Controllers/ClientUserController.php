<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\ClientUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class ClientUserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $clientUsers = ClientUser::with(['client', 'loginSessions' => function ($query) {
            $query->latest('logged_in_at')->limit(1);
        }])
        ->latest()
        ->paginate(20);

        return inertia('ClientUsers/Index', [
            'clientUsers' => $clientUsers,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $clients = Client::orderBy('name')->get();

        return inertia('ClientUsers/Create', [
            'clients' => $clients,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'      => 'required|string|max:255',
            'email'     => 'required|string|email|max:255|unique:client_users',
            'password'  => 'required|string|min:8|confirmed',
            'client_id' => 'required|exists:clients,id',
            'is_active' => 'boolean',
        ]);

        ClientUser::create([
            'name'      => $request->name,
            'email'     => $request->email,
            'password'  => Hash::make($request->password),
            'client_id' => $request->client_id,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()->route('client-users.index')
            ->with('success', 'Client user created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(ClientUser $clientUser)
    {
        $clientUser->load(['client', 'loginSessions' => function ($query) {
            $query->latest('logged_in_at')->limit(10);
        }]);

        return inertia('ClientUsers/Show', [
            'clientUser' => $clientUser,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ClientUser $clientUser)
    {
        $clients = Client::orderBy('name')->get();

        return inertia('ClientUsers/Edit', [
            'clientUser' => $clientUser,
            'clients'    => $clients,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ClientUser $clientUser)
    {
        $request->validate([
            'name'  => 'required|string|max:255',
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('client_users')->ignore($clientUser->id),
            ],
            'password'  => 'nullable|string|min:8|confirmed',
            'client_id' => 'required|exists:clients,id',
            'is_active' => 'boolean',
        ]);

        $data = [
            'name'      => $request->name,
            'email'     => $request->email,
            'client_id' => $request->client_id,
            'is_active' => $request->boolean('is_active', true),
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $clientUser->update($data);

        return redirect()->route('client-users.index')
            ->with('success', 'Client user updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ClientUser $clientUser)
    {
        $clientUser->delete();

        return redirect()->route('client-users.index')
            ->with('success', 'Client user deleted successfully.');
    }

    /**
     * Log out all active sessions for a client user.
     */
    public function logoutAllSessions(ClientUser $clientUser)
    {
        $clientUser->logOutAllSessions();

        return redirect()->back()
            ->with('success', 'All active sessions have been logged out.');
    }
}
