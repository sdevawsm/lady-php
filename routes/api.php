<?php

use LadyPHP\Http\Response;

// Rota de exemplo da API
$router->get('/status', function() {
    return Response::json([
        'status' => 'online',
        'version' => '1.0.0'
    ]);
}); 