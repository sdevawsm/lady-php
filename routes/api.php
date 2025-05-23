<?php

use LadyPHP\Routing\RouteFacade as Route;
use LadyPHP\Http\Response;

// Rota de exemplo da API
Route::get('/status', function() {
    return Response::json([
        'status' => 'online',
        'version' => '1.0.0'
    ]);
});