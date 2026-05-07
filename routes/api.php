<?php

use App\Http\Controllers\Api\V1\ClientController;
use App\Http\Controllers\Api\V1\InvoiceController;
use App\Http\Controllers\Api\V1\ProjectController;
use App\Http\Controllers\Api\V1\SessionController;
use App\Http\Controllers\Api\V1\SprintController;
use App\Http\Controllers\Api\V1\TaskController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::prefix('v1')->name('api.v1.')->middleware('auth:sanctum')->group(function () {
    Route::get('user', fn (Request $request) => $request->user())->name('user');

    Route::apiResource('clients', ClientController::class);
    Route::apiResource('projects', ProjectController::class);
    Route::patch('projects/{project}/archive', [ProjectController::class, 'archive'])->name('projects.archive');
    Route::patch('projects/{project}/unarchive', [ProjectController::class, 'unarchive'])->name('projects.unarchive');

    Route::apiResource('tasks', TaskController::class);

    Route::post('sessions/start', [SessionController::class, 'start'])->name('sessions.start');
    Route::post('sessions/stop', [SessionController::class, 'stop'])->name('sessions.stop');
    Route::post('sessions/{session}/continue', [SessionController::class, 'continue'])->name('sessions.continue');
    Route::apiResource('sessions', SessionController::class);

    Route::apiResource('sprints', SprintController::class);
    Route::apiResource('invoices', InvoiceController::class);
});
