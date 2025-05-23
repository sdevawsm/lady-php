<?php

use LadyPHP\Routing\RouteFacade as Route;
use LadyPHP\Http\Response;

// Grupo de rotas da API v1 com logging e prefixo 'v1'
Route::group([
    'prefix' => 'v1',
    'middleware' => 'App\Http\Middleware\LogMiddleware'
], function() {
    // Rota de status da API
    Route::get('/status', function() {
        return Response::json([
            'status' => 'online',
            'version' => '1.0.0'
        ]);
    });

    // Grupo de rotas administrativas com autenticação
    Route::group(['middleware' => 'App\Http\Middleware\AuthMiddleware'], function() {
        Route::get('/admin', function() {
            return Response::json([
                'message' => 'Painel Administrativo',
                'user' => 'admin'
            ]);
        });

        // Você pode adicionar mais rotas administrativas aqui
        Route::get('/admin/users', function() {
            return Response::json([
                'users' => ['admin', 'user1', 'user2']
            ]);
        });
    });
});

// Grupo de rotas da API v2 com logging e prefixo 'v2'
Route::group([
    'prefix' => 'v2',
    'middleware' => 'App\Http\Middleware\LogMiddleware'
], function() {
    Route::get('/status', function() {
        return Response::json([
            'status' => 'online',
            'version' => '2.0.0',
            'features' => ['new-feature-1', 'new-feature-2']
        ]);
    });
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