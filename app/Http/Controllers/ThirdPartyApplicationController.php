<?php

namespace App\Http\Controllers;

use App\Models\ThirdPartyApplication;

class ThirdPartyApplicationController extends Controller
{
    public function store()
    {
        ThirdPartyApplication::create([
            'type' => request('type'),
            'name' => request('name'),
        ]);

        return redirect()->route('third-party-application.index');
    }
}
