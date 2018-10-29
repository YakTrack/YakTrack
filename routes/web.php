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

Auth::routes(['verify' => true]);

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
    Route::resource('third-party-application', 'ThirdPartyApplicationController');
    Route::resource('third-party-application-session', 'ThirdPartyApplicationSessionController');
    Route::resource('invoice', 'InvoiceController');
    Route::resource('invoice.session', 'Invoice\SessionController');

    Route::get('session/start', 'SessionController@start')->name('session.start');
    Route::get('session/stop', 'SessionController@stop')->name('session.stop');

    Route::resource('session', 'SessionController');
});
