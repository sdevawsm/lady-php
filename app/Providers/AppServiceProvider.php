<?php

namespace App\Providers;

use LadyPHP\Core\Application;

class AppServiceProvider
{
    protected Application $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * Registra os serviços da aplicação
     */
    public function register(): void
    {
        // Registra serviços básicos aqui
    }

    /**
     * Inicializa os serviços da aplicação
     */
    public function boot(): void
    {
        // Inicializa serviços aqui
    }
} 