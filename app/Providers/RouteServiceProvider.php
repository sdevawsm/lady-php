<?php

namespace App\Providers;

use LadyPHP\Core\Application;
use LadyPHP\Routing\RouteFacade as Route;
use LadyPHP\Routing\Router;

class RouteServiceProvider extends AppServiceProvider
{
    /**
     * Define o caminho para os arquivos de rotas da aplicação
     */
    protected string $routesPath = __DIR__ . '/../../routes';

    /**
     * Carrega as rotas da aplicação
     */
    public function boot(): void
    {
        $this->loadRoutes();
    }

    /**
     * Carrega os arquivos de rotas
     */
    protected function loadRoutes(): void
    {
        // Carrega as rotas web
        if (file_exists($this->routesPath . '/web.php')) {
            require $this->routesPath . '/web.php';
        }

        // Carrega as rotas da API
        if (file_exists($this->routesPath . '/api.php')) {
            Route::group(['prefix' => 'api'], function(Router $router) {
                require $this->routesPath . '/api.php';
            });
        }
    }
} 