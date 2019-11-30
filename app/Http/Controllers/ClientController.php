<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ClientController extends Controller
{
    /**
     * Display a listing of clients.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Inertia::render('Client/Index', [
            'clients' => \App\Models\Client::orderBy('name')->get(),
        ]);
    }

    /**
     * Show the form for creating a new client.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return Inertia::render('Client/Edit');
    }

    /**
     * Store a newly created form in storage.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $client = Client::create($request->validate([
            'name'  => 'required',
            'email' => 'email',
        ]));

        return redirect()
            ->route('client.index')
            ->with('success', 'You have created client "' . $client->name . '"');
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Client $client)
    {
        return Inertia::render('Client/Show', ['client' => $client]);
    }

    /**
     * Show the form for editing the specified client.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(Client $client)
    {
        return Inertia::render('Client/Edit', [
            'client' => $client,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int                      $id
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Client $client)
    {
        $this->validate($request, [
            'name'  => 'required',
            'email' => 'email',
        ]);

        $client->name  = $request->name;
        $client->email = $request->email;
        $client->save();

        return redirect()
            ->route('client.index')
            ->with('success', 'You have updated client "' . $client->name . '"');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Client $client
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Client $client)
    {
        $client->delete();

        return redirect()
            ->route('client.index')
            ->with('success', 'You have deleted client "' . $client->name . '"');
    }
}
