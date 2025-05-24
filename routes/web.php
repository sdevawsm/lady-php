<?php

use LadyPHP\Routing\RouteFacade as Route;
use LadyPHP\Http\Response;

// Rota de exemplo
Route::get('/', function() {
    return new Response('Bem-vindo ao LadyPHP!');
});


Route::get('/login', function() {
    return new Response('
        <!DOCTYPE html>
        <html>
        <head>
            <title>Formulário de Teste - Validações</title>
            <style>
                body {
                    font-family: Arial, sans-serif;
                    max-width: 600px;
                    margin: 20px auto;
                    padding: 20px;
                }
                .form-group {
                    margin-bottom: 15px;
                }
                label {
                    display: block;
                    margin-bottom: 5px;
                    font-weight: bold;
                }
                input, select {
                    width: 100%;
                    padding: 8px;
                    margin-bottom: 10px;
                    border: 1px solid #ddd;
                    border-radius: 4px;
                }
                button {
                    background-color: #4CAF50;
                    color: white;
                    padding: 10px 15px;
                    border: none;
                    border-radius: 4px;
                    cursor: pointer;
                }
                button:hover {
                    background-color: #45a049;
                }
            </style>
        </head>
        <body>
            <h2>Formulário de Teste - Validações</h2>
            <form action="/login" method="post">
                <div class="form-group">
                    <label for="name">Nome Completo:</label>
                    <input type="text" id="name" name="name" placeholder="Digite seu nome completo">
                </div>

                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" placeholder="Digite seu email">
                </div>

                <div class="form-group">
                    <label for="password">Senha:</label>
                    <input type="password" id="password" name="password" placeholder="Digite sua senha">
                </div>

                <div class="form-group">
                    <label for="age">Idade:</label>
                    <input type="number" id="age" name="age" placeholder="Digite sua idade">
                </div>

                <div class="form-group">
                    <label for="website">Website:</label>
                    <input type="url" id="website" name="website" placeholder="Digite seu website (opcional)">
                </div>

                <div class="form-group">
                    <label for="phone">Telefone:</label>
                    <input type="tel" id="phone" name="phone" placeholder="Digite seu telefone">
                </div>

                <div class="form-group">
                    <label for="interests">Interesses:</label>
                    <select id="interests" name="interests[]" multiple>
                        <option value="php">PHP</option>
                        <option value="javascript">JavaScript</option>
                        <option value="python">Python</option>
                        <option value="java">Java</option>
                    </select>
                </div>

                <button type="submit">Enviar</button>
            </form>
        </body>
        </html>
    ');
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

