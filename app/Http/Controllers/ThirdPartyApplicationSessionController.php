<?php

namespace App\Http\Controllers;

use App\Models\ThirdPartyApplicationSession;
use Illuminate\Http\Request;

class ThirdPartyApplicationSessionController extends Controller
{
    public function store()
    {
        ThirdPartyApplicationSession::create(request()->validate([
            'session_id' => 'required|exists:sessions,id',
            'third_party_application_id' => 'required|exists:third_party_applications,id',
        ]));

        return redirect()->back();
    }
}
