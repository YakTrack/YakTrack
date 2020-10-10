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
}
