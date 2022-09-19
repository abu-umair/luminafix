<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', 'Auth\LoginController@showLoginForm');
Auth::routes();

Route::prefix('dashboard')
    ->namespace('Dashboard') //yang di controller (namespace App\Http\Controllers\Admin;)
    ->middleware([ 'auth','role:admin']) // ini diisi setelah instal middleware(satpam)
    // auth & admin, cek di kernel ada auth & admin
    ->group(function () {
        //Route::get('/', 'DashboardController@index')
        //    ->name('dashboard'); //menamakan route ini
// adfood
        // Route::get('/', 'DashboardAdfoodController@index')
        //     ->name('dashboard-adfood');
// adfood
        // Route::get('/notification', 'DashboardController@allNotif')
        //     ->name('notificat'); //menamakan route ini   

        // Route::resource('appointments-ongoing', 'OngoingController');
        // Route::resource('users', 'Users_LuminaController');
        // Route::get('regisku', 'Users_LuminaController@signUp');

        // User management
        // Route::resource('users', 'Users_LuminaController')
        // ->except('create', 'edit')
        // ->names([
        //     'index' => 'users',
        //     'store' => 'users.create',
        //     'destroy' => 'users.delete'
        // ]);
    });
    


Route::group([
    'middleware' => ['auth', 'role:admin'],
    'prefix' => 'dashboard',
    'namespace' => 'Dashboard',
    ], function () {

    Route::get('edit-student/{id}', 'ProjectsController@showproject');
    Route::get('showproject/{id}', 'ProjectsController@showproject');

    Route::get('/', 'DashboardController@index')->name('dashboard');

    Route::name('dashboard.')->group(function () {

        Route::get('fee-index', 'FeeIndexesController@index')->name('fee-index');

        Route::group([
            'prefix' => 'variables',
            'controller' => 'VariablesController'
        ], function () {
            Route::get('/', 'index')->name('variables');
            Route::put('/', 'update')->name('variables.update');
        });

        Route::group([
            'prefix' => 'profile',
            'controller' => 'ProfileController'
        ], function () {
            Route::get('/', 'index')->name('profile');
            Route::put('/', 'update')->name('profile.update');
        });

        

        // User management
        Route::resource('users', 'UsersController')
            ->except('create', 'edit')
            ->names([
                'index' => 'users',
                'store' => 'users.create',
                'destroy' => 'users.delete'
            ]);

        // Projects management
        Route::resource('projects', 'ProjectsController')
            ->except('create', 'edit')
            ->names([
                'index' => 'projects',
                'store' => 'projects.create',
                'destroy' => 'projects.delete'
            ]);

        // Invoice management
        Route::resource('invoices', 'InvoicesController')
            ->except('create', 'edit')
            ->names([
                'index' => 'invoices',
                'store' => 'invoices.create',
                'destroy' => 'invoices.delete'
            ]);

        // Budget and Expenses
        Route::resource('costs', 'CostsController')
            ->except('create', 'edit')
            ->names([
                'index' => 'costs',
                'store' => 'costs.create',
                'destroy' => 'costs.delete'
            ]);
    });
});
