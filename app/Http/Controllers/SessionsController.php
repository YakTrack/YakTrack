<?php

namespace App\Http\Controllers;

use App\Models\Session;
use Illuminate\Http\Request;

class SessionsController extends Controller
{
    public function update()
    {
        request()->validate([
            'sessions.*' => 'required|exists:sessions,id',
            'sessions.*' => 'array',
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
