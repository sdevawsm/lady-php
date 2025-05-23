<?php

use App\Providers\AppServiceProvider;
use App\Providers\RouteServiceProvider;
use LadyPHP\Core\Application;

// Inicializa a aplicação
$app = new Application();

// Registra os providers
$providers = [
    AppServiceProvider::class,
    RouteServiceProvider::class,
];

foreach ($providers as $provider) {
    $provider = new $provider($app);
    $provider->register();
    $provider->boot();
}

return $app; 