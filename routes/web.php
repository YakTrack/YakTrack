<?php

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
    Route::resource('task', 'TaskController');
    Route::resource('task.session', 'Task\SessionController');
    Route::resource('third-party-application', 'ThirdPartyApplicationController');
    Route::resource('third-party-application-session', 'ThirdPartyApplicationSessionController');
    Route::resource('invoice', 'InvoiceController');
    Route::resource('invoice.session', 'Invoice\SessionController');

    Route::get('session/start', 'SessionController@start')->name('session.start');
    Route::get('session/stop', 'SessionController@stop')->name('session.stop');

    Route::resource('session', 'SessionController');

    Route::get('wherehas', function () {
        $invoices = App\Models\Invoice::has('sessions')->get();
    });
});
