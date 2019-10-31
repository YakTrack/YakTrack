<?php

namespace App\Providers;

use Auth;
use Illuminate\Support\ServiceProvider;
use Inertia\Inertia;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        Inertia::setRootView('layouts.app');

        Inertia::version(function () {
            return md5_file(public_path('mix-manifest.json'));
        });

        Inertia::share([
            'app' => [
                'name'      => config('app.name'),
                'csrfToken' => csrf_token(),
            ],
            'auth' => function () {
                return [
                    'user' => Auth::user() ? [
                        'id'         => Auth::user()->id,
                        'name'       => Auth::user()->name,
                        'email'      => Auth::user()->email,
                    ] : null
                ];
            }
        ]);
    }
}
