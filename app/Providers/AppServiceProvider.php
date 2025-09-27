<?php

namespace App\Providers;

use App\Models\User;
use Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;
use Inertia\Inertia;
use Session;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        /*
         * Enforce foreign key constraints if testing with sqlite
         */
        if (DB::connection() instanceof \Illuminate\Database\SQLiteConnection) {
            DB::statement('PRAGMA foreign_keys=1');
        }
    }

    /**
     * Register any application services.
     *
     */
    public function register(): void
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
                $user = Auth::user();
                return [
                    'user' => $user instanceof User ? [
                        'id'         => $user->id,
                        'name'       => $user->name,
                        'email'      => $user->email,
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
