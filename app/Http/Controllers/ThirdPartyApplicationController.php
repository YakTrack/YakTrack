<?php

namespace App\Http\Controllers;

use App\Models\ThirdPartyApplication;
use Illuminate\Http\RedirectResponse;

class ThirdPartyApplicationController extends Controller
{
    public function store(): RedirectResponse
    {
        ThirdPartyApplication::create([
            'type' => request('type'),
            'name' => request('name'),
        ]);

        return redirect()->route('third-party-application.index');
    }
}
