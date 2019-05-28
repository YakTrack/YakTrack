<?php

/*
|--------------------------------------------------------------------------
| JSON Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application
| which should return JSON
|
*/

Route::group(['middleware' => 'auth'], function () {
    Route::resource('session', 'Json\SessionController');
});
