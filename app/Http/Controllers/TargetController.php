<?php

namespace App\Http\Controllers;

use App\Models\Target;
use App\Rules\TargetDoesNotAlreadyExist;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class TargetController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Target/Index', [
            'targets' => Target::orderBy('starts_at', 'desc')->limit(1000)->get(),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Target/Edit', [
            'target' => new Target(),
        ]);
    }

    public function store(): RedirectResponse
    {
        request()->validate([
            'starts_at' => new TargetDoesNotAlreadyExist(request()->only([
                'duration_unit',
                'duration',
            ])),
        ]);

        Target::create([
            'value_unit'    => request('value_unit'),
            'value'         => request('value'),
            'duration_unit' => request('duration_unit'),
            'duration'      => request('duration'),
            'starts_at'     => request('starts_at'),
            'billable_only' => request('billable_only'),
        ]);

        return redirect(route('target.index'));
    }
}
