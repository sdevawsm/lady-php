<?php

use LadyPHP\Routing\RouteFacade as Route;
use LadyPHP\Http\Response;

// Grupo de rotas da API v1 com logging e prefixo 'v1'
Route::group([
    'prefix' => 'v1',
    'middleware' => 'App\Http\Middleware\LogMiddleware'
], function() {
    // Rotas de status da API
    Route::get('/status', 'Api\StatusController@index');
    Route::get('/status/details', 'Api\StatusController@details');

    // Grupo de rotas administrativas com autenticação
    Route::group(['middleware' => 'App\Http\Middleware\AuthMiddleware'], function() {
        Route::get('/admin', 'Admin\DashboardController@index');
        Route::get('/admin/users', 'Admin\DashboardController@users');
        Route::get('/admin/settings', 'Admin\DashboardController@settings');
    });
});

// Grupo de rotas da API v2 com logging e prefixo 'v2'
Route::group([
    'prefix' => 'v2',
    'middleware' => 'App\Http\Middleware\LogMiddleware'
], function() {
    Route::get('/status', 'Api\StatusController@index');
});



/*


Route::get('/admin', 'AdminController@index')
    ->middleware('App\Http\Middleware\AuthMiddleware');

Route::group(['middleware' => 'App\Http\Middleware\AuthMiddleware'], function() {
    Route::get('/admin/users', 'UserController@index');
    Route::get('/admin/settings', 'SettingsController@index');
});


Route::get('/admin', 'AdminController@index')
    ->middleware([
        'App\Http\Middleware\AuthMiddleware',
        'App\Http\Middleware\LogMiddleware'
    ]);

*/