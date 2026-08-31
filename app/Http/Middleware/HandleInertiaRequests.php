<?php

namespace App\Http\Middleware;

use App\Models\Client;
use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return string|null
     */
    public function version(Request $request)
    {
        return parent::version($request);
    }

    /**
     * Defines the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        $shared = array_merge(parent::share($request), [
            'auth' => [
                'user' => $request->user() ? [
                    'id'    => $request->user()->id,
                    'name'  => $request->user()->name,
                    'email' => $request->user()->email,
                ] : null,
                'clientUser' => $request->user('client') ? [
                    'id'    => $request->user('client')->id,
                    'name'  => $request->user('client')->name,
                    'email' => $request->user('client')->email,
                ] : null,
            ],
        ]);

        // Focus data is a web-user convenience only; never expose it to client-portal users.
        // Gate explicitly on the web guard: the auth:client middleware promotes 'client' to the
        // default guard, so $request->user() would otherwise resolve the client-portal user here.
        if ($user = $request->user('web')) {
            $focusedClient = $user->focusedClient;

            $shared['focusableClients'] = Client::orderBy('name')->get(['id', 'name']);
            $shared['focusedClient'] = $focusedClient ? [
                'id'   => $focusedClient->id,
                'name' => $focusedClient->name,
            ] : null;
        }

        return $shared;
    }
}
