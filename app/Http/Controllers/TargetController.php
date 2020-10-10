<?php

namespace App\Http\Controllers;

use App\Models\Target;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TargetController extends Controller
{
    public function index()
    {
        return Inertia::render('Target/Index', [
            'targets' => Target::orderBy('id', 'desc')->limit(1000)->get(),
        ]);
    }

    public function create()
    {
        return Inertia::render('Target/Edit', [
            'target' => new Target,
        ]);
    }

    public function store()
    {
        Target::create([
            'value_unit' => request('value_unit'),
            'value' => request('value'),
            'duration_unit' => request('duration_unit'),
            'duration' => request('duration'),
            'starts_at' => request('starts_at'),
            'billable_only' => request('billable_only'),
        ]);

        return redirect(route('target.index'));
    }
}
