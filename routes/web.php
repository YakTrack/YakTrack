<?php

use App\Models\User;
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

if (app()->environment('testing')) {
    Route::get('/__testing/browser-login/{user}', function (User $user) {
        auth()->login($user);

        return redirect()->route('home');
    })->name('testing.browser-login');
}

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
    Route::get('project/{project}/kanban', 'ProjectController@kanban')->name('project.kanban');
    Route::post('project/{project}/jira', 'ProjectJiraController@store')->name('project.jira.store');
    Route::delete('project/{project}/jira', 'ProjectJiraController@destroy')->name('project.jira.destroy');
    Route::get('project/{project}/jira/issues/search', 'ProjectJiraController@searchIssues')->name('project.jira.issues.search');
    Route::get('project/{project}/jira/issues/preview', 'ProjectJiraController@previewIssue')->name('project.jira.issues.preview');
    Route::post('project/{project}/jira/import', 'ProjectJiraController@import')->name('project.jira.import');
    Route::patch('project/{project}/archive', 'ProjectController@archive')->name('project.archive');
    Route::patch('project/{project}/unarchive', 'ProjectController@unarchive')->name('project.unarchive');
    Route::get('project-archived', 'ProjectController@archived')->name('project.archived');
    Route::resource('sprint', 'SprintController');
    Route::resource('sprint.invoice', 'Sprint\InvoiceController');
    Route::resource('target', 'TargetController');
    Route::patch('task/bulk-assign-project', 'TaskController@bulkAssignProject')->name('task.bulk-assign-project');
    Route::resource('task', 'TaskController');
    Route::patch('task/{task}/status', 'TaskController@updateStatus')->name('task.updateStatus');
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
    Route::get('session/{session}/edit-form', 'SessionController@editForm')->name('session.edit-form');

    Route::resource('session', 'SessionController');
    Route::patch('sessions', 'SessionsController@update')->name('sessions.update');
    Route::post('sessions/destroy-many', 'SessionsController@destroyMany')->name('sessions.destroy-many');

    // Acceptance Criteria Routes
    Route::resource('acceptance-criteria', 'AcceptanceCriteriaController');
    Route::get('acceptance-criteria-import', 'AcceptanceCriteriaController@import')->name('acceptance-criteria.import');
    Route::post('acceptance-criteria-import', 'AcceptanceCriteriaController@processImport')->name('acceptance-criteria.import.process');

    // Feature Routes
    Route::resource('features', 'FeatureController');

    // Test Run Routes
    Route::resource('test-run', 'TestRunController');
    Route::post('test-run/{testRun}/test-result', 'TestRunController@storeTestResult')->name('test-run.test-result.store');

    // Test Result Routes
    Route::patch('test-result/{testResult}', 'TestResultController@update')->name('test-result.update');
    Route::post('test-result/{testResult}/evidence', 'TestResultController@addEvidence')->name('test-result.evidence.store');
    Route::delete('test-result-evidence/{evidence}', 'TestResultController@removeEvidence')->name('test-result.evidence.destroy');

    // Client User Management Routes
    Route::resource('client-users', 'ClientUserController');
    Route::post('client-users/{clientUser}/logout-all-sessions', 'ClientUserController@logoutAllSessions')->name('client-users.logout-all-sessions');

    // Client Login Session Routes
    Route::get('client-login-sessions', 'ClientLoginSessionController@index')->name('client-login-sessions.index');
    Route::get('client-login-sessions/statistics', 'ClientLoginSessionController@statistics')->name('client-login-sessions.statistics');
    Route::get('client-login-sessions/{session}', 'ClientLoginSessionController@show')->name('client-login-sessions.show');
    Route::post('client-login-sessions/{session}/logout', 'ClientLoginSessionController@logout')->name('client-login-sessions.logout');
    Route::get('client-users/{clientUser}/login-sessions', 'ClientLoginSessionController@forClientUser')->name('client-users.login-sessions');
    Route::post('client-users/{clientUser}/logout-all-sessions', 'ClientLoginSessionController@logoutAllForUser')->name('client-users.logout-all-sessions');

    // API Token Management
    Route::resource('api-tokens', 'ApiTokenController')->only(['index', 'create', 'store', 'destroy']);
});

// Client Portal Routes
Route::prefix('client-portal')->name('client-portal.')->group(function () {
    // Authentication routes
    Route::get('login', 'ClientPortal\AuthController@showLoginForm')->name('login');
    Route::post('login', 'ClientPortal\AuthController@login')->name('login.attempt');
    Route::post('logout', 'ClientPortal\AuthController@logout')->name('logout');

    // Protected routes
    Route::middleware(['auth:client', 'log.client.login'])->group(function () {
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
