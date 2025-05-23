<?php

use LadyPHP\Routing\RouteFacade as Route;

if (!function_exists('route')) {
    /**
     * Retorna uma instância da fachada de rotas
     *
     * @return \LadyPHP\Routing\RouteFacade
     */
    function route(): \LadyPHP\Routing\RouteFacade
    {
        return Route::class;
    }
} 