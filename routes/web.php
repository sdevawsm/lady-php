<?php

use LadyPHP\Routing\RouteFacade as Route;
use LadyPHP\Http\Response;

// Rota de exemplo
Route::get('/', function() {
    return new Response('Bem-vindo ao LadyPHP!');
});


Route::group(['prefix' => 'v1'], function() {
    Route::get('/dashboard', function() {
        return new Response('Painel Administrativo');
    });
    
    Route::get('/users', function() {
        return new Response('Lista de Usuários');
    });
});

// Exemplo de grupo de rotas
/*Route::group(['prefix' => 'admin'], function() {
    Route::get('/dashboard', function() {
        return new Response('Painel Administrativo');
    });
    
    Route::get('/users', function() {
        return new Response('Lista de Usuários');
    });
}); */

