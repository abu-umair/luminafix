<?php

use App\Helpers\JSON;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
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

// Auth routes
Route::group([
    'prefix' => 'login',
    'controller' => 'Auth/LoginController'
], function () {
    Route::post('firebase', 'Auth\LoginController@firebase')->name('login.firebase');
    Route::post('password', 'Auth\LoginController@password')->name('login.password');
});

// Protected routes
Route::middleware(['auth:api'])->group(function () {

    // Projects management
    Route::resource('projects', 'ProjectsController')
        ->only(['index', 'show', 'update'])
        ->names(['index' => 'projects']);

    Route::get('/', 'InitialDataController@index')->name('dashboard');
});
