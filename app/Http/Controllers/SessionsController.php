<?php

namespace App\Http\Controllers;

use App\Models\Session;

class SessionsController extends Controller
{
    public function update()
    {
        request()->validate([
            'sessions.*' => 'required|array|exists:sessions,id',
        ]);

        $sessions = collect(request('sessions'))
            ->keys()
            ->map(function ($id) {
                return Session::find($id);
            })->each(function ($session) {
                $session->update(request('sessions')[$session->id]);
            });

        return redirect()->back();
    }
}
