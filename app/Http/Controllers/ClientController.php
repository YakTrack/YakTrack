<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ClientController extends Controller
{
    /**
     * Display a listing of clients.
     */
    public function index(): Response
    {
        return Inertia::render('Client/Index', [
            'clients' => \App\Models\Client::orderBy('name')->get(),
        ]);
    }

    /**
     * Show the form for creating a new client.
     */
    public function create(): Response
    {
        return Inertia::render('Client/Edit');
    }

    /**
     * Store a newly created form in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $client = Client::create($request->validate([
            'name'  => 'required',
            'email' => 'email',
        ]));

        return redirect()
            ->route('client.index')
            ->with('success', 'You have created client "'.$client->name.'"');
    }

    /**
     * Display the specified resource.
     */
    public function show(Client $client): Response
    {
        return Inertia::render('Client/Show', ['client' => $client]);
    }

    /**
     * Show the form for editing the specified client.
     */
    public function edit(Client $client): Response
    {
        return Inertia::render('Client/Edit', [
            'client' => $client,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Client $client): RedirectResponse
    {
        $this->validate($request, [
            'name'  => 'required',
            'email' => 'email',
        ]);

        $client->name = $request->name;
        $client->email = $request->email;
        $client->save();

        return redirect()
            ->route('client.index')
            ->with('success', 'You have updated client "'.$client->name.'"');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Client $client): RedirectResponse
    {
        $client->delete();

        return redirect()
            ->route('client.index')
            ->with('success', 'You have deleted client "'.$client->name.'"');
    }
}
