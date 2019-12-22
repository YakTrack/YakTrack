<?php

namespace App\Providers;

use Auth;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Session;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        /**
         * Enforce foreign key constraints if testing with sqlite
         */
        if (DB::connection() instanceof \Illuminate\Database\SQLiteConnection) {
            DB::statement(DB::raw('PRAGMA foreign_keys=1'));
        }
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
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
                    ] : null,
                ];
            },
            'flash' => function () {
                return [
                    'success' => Session::get('success'),
                    'error'   => Session::get('error'),
                ];
            },
            'errors' => function () {
                return Session::get('errors')
                    ? Session::get('errors')->getBag('default')->getMessages()
                    : (object) [];
            },
        ]);
    }
}
