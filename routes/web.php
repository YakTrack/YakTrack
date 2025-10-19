<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

// Auth
Route::get('login')->name('login')->uses('Auth\LoginController@showLoginForm')->middleware('guest');
Route::post('login')->name('login.attempt')->uses('Auth\LoginController@login')->middleware('guest');
Route::post('logout')->name('logout')->uses('Auth\LoginController@logout');

if (!config('app.allow_registration')) {
    Route::any('/register', function () {
        abort(403);
    });
}

Route::group(['middleware' => 'auth'], function () {
    Route::get('/', [
        'as'   => 'home',
        'uses' => 'HomeController@index',
    ]);

    Route::resource('client', 'ClientController');
    Route::resource('project', 'ProjectController');
    Route::resource('sprint', 'SprintController');
    Route::resource('sprint.invoice', 'Sprint\InvoiceController');
    Route::resource('target', 'TargetController');
    Route::resource('task', 'TaskController');
    Route::resource('task-status', 'TaskStatusController');
    Route::resource('session-category', 'SessionCategoryController');
    Route::resource('third-party-application', 'ThirdPartyApplicationController');
    Route::resource('third-party-application-session', 'ThirdPartyApplicationSessionController');
    Route::resource('invoice', 'InvoiceController');
    Route::resource('invoice.session', 'Invoice\SessionController');

    Route::post('session/start', 'SessionController@start')->name('session.start');
    Route::post('session/stop', 'SessionController@stop')->name('session.stop');
    Route::post('session/{session}/continue', 'SessionController@continue')->name('session.continue');
    Route::post('session/{session}/split', 'SessionController@split')->name('session.split');

    Route::resource('session', 'SessionController');
    Route::patch('sessions', 'SessionsController@update')->name('sessions.update');

    // Acceptance Criteria Routes
    Route::resource('acceptance-criteria', 'AcceptanceCriteriaController');
    Route::get('acceptance-criteria-import', 'AcceptanceCriteriaController@import')->name('acceptance-criteria.import');
    Route::post('acceptance-criteria-import', 'AcceptanceCriteriaController@processImport')->name('acceptance-criteria.import.process');

    // Test Run Routes
    Route::resource('test-run', 'TestRunController');

    // Test Result Routes
    Route::patch('test-result/{testResult}', 'TestResultController@update')->name('test-result.update');
    Route::post('test-result/{testResult}/evidence', 'TestResultController@addEvidence')->name('test-result.evidence.store');
    Route::delete('test-result-evidence/{evidence}', 'TestResultController@removeEvidence')->name('test-result.evidence.destroy');
});

// Client Portal Routes
Route::prefix('client-portal')->name('client-portal.')->group(function () {
    // Authentication routes
    Route::get('login', 'ClientPortal\AuthController@showLoginForm')->name('login');
    Route::post('login', 'ClientPortal\AuthController@login')->name('login.attempt');
    Route::post('logout', 'ClientPortal\AuthController@logout')->name('logout');

    // Protected routes
    Route::middleware('auth:client')->group(function () {
        Route::get('/', 'ClientPortal\DashboardController@index')->name('dashboard');

        Route::get('projects', 'ClientPortal\ProjectController@index')->name('projects.index');
        Route::get('projects/{project}', 'ClientPortal\ProjectController@show')->name('projects.show');
        Route::get('projects/{project}/report', 'ClientPortal\ProjectController@downloadReport')->name('projects.report');
        Route::get('projects/{project}/report/view', 'ClientPortal\ProjectController@viewReport')->name('projects.report.view');

        Route::get('tasks', 'ClientPortal\TaskController@index')->name('tasks.index');
        Route::get('tasks/{task}', 'ClientPortal\TaskController@show')->name('tasks.show');

        Route::get('sessions', 'ClientPortal\SessionController@index')->name('sessions.index');
        Route::get('sessions/{session}', 'ClientPortal\SessionController@show')->name('sessions.show');
    });
});
