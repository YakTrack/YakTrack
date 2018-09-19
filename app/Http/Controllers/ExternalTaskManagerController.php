<?php

namespace App\Http\Controllers;

use App\Models\ExternalTaskManager;

class ExternalTaskManagerController extends Controller
{
    public function store()
    {
        ExternalTaskManager::create([
            'type' => request('type'),
            'name' => request('name'),
        ]);

        return redirect()->route('external-task-manager.index');
    }
}
