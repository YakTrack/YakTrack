<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateFocusedClientRequest;
use App\Models\Client;
use Illuminate\Http\RedirectResponse;

class FocusedClientController extends Controller
{
    /**
     * Set or clear the authenticated user's focused client.
     */
    public function update(UpdateFocusedClientRequest $request): RedirectResponse
    {
        $clientId = $request->validated('client_id');

        $request->user()->update([
            'focused_client_id' => $clientId,
        ]);

        $message = $clientId
            ? 'Now focusing on '.Client::findOrFail($clientId)->name.'.'
            : 'Focus cleared. Showing all clients.';

        return redirect()
            ->back()
            ->with('success', $message);
    }
}
