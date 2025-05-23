<?php

use LadyPHP\Routing\RouteFacade as Route;
use LadyPHP\Http\Response;

// Rota de exemplo
Route::get('/', function() {
    return new Response('Bem-vindo ao LadyPHP!');
});


Route::get('/login', function() {
    return new Response('<form action="/login" method="post">
        <input type="text" name="email" placeholder="Email">
        <input type="password" name="password" placeholder="Senha">
        <button type="submit">Login</button>
    </form>');
});



Route::post('/login', 'Auth\LoginController@login');

Route::group(['prefix' => 'v1'], function() {
    Route::get('/dashboard', 'Admin\DashboardController@index');
    
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

