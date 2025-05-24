<?php

use LadyPHP\Routing\RouteFacade as Route;
use LadyPHP\Http\Response;

// Rota principal
Route::get('/', 'WelcomeController@index');


// Exemplo de grupo de rotas
/*Route::group(['prefix' => 'admin'], function() {
    Route::get('/dashboard', function() {
        return new Response('Painel Administrativo');
    });
    
    Route::get('/users', function() {
        return new Response('Lista de Usuários');
    });
}); */

