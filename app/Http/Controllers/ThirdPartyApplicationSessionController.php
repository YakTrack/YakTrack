<?php

namespace App\Http\Controllers;

use App\Models\Session;
use App\Models\ThirdPartyApplication;
use Illuminate\Http\Request;

class ThirdPartyApplicationSessionController extends Controller
{
    public function store()
    {
        request()->validate([
            'session_id'                 => 'required|exists:sessions,id',
            'third_party_application_id' => 'required|exists:third_party_applications,id',
        ]);

        Session::find(request('session_id'))->linkTo(ThirdPartyApplication::find(request('third_party_application_id')));

        return redirect()->back();
    }
}
