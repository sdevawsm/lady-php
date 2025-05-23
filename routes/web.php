<?php

use LadyPHP\Http\Response;

// Rota de exemplo
$router->get('/', function() {
    return new Response('Bem-vindo ao LadyPHP!');
}); 