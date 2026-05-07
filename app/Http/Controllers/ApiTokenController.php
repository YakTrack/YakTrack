<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Laravel\Sanctum\PersonalAccessToken;

class ApiTokenController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('ApiToken/Index', [
            'tokens' => auth()->user()->tokens()
                ->orderByDesc('created_at')
                ->get()
                ->map(fn (PersonalAccessToken $token) => [
                    'id'           => $token->id,
                    'name'         => $token->name,
                    'last_used_at' => $token->last_used_at?->diffForHumans(),
                    'created_at'   => $token->created_at->format('j M Y'),
                ]),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('ApiToken/Create');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $token = auth()->user()->createToken($request->name);

        return redirect()
            ->route('api-tokens.index')
            ->with('success', 'Token "'.$request->name.'" created successfully.')
            ->with('newToken', $token->plainTextToken);
    }

    public function destroy(PersonalAccessToken $api_token): RedirectResponse
    {
        $api_token->delete();

        return redirect()
            ->route('api-tokens.index')
            ->with('success', 'Token revoked.');
    }
}
