<?php

namespace LadyPHP\Core;

use LadyPHP\Http\Request;
use LadyPHP\Routing\Router;

class Application
{
    protected Router $router;
    protected Request $request;
    protected static ?Application $instance = null;

    public function __construct()
    {
        self::$instance = $this;
        $this->request = new Request();
        $this->router = new Router();
    }

    public static function getInstance(): ?Application
    {
        return self::$instance;
    }

    public function run(): void
    {
        // Processa a requisição através do router
        $response = $this->router->dispatch($this->request);
        
        // Envia a resposta
        $response->send();
    }

    public function getRouter(): Router
    {
        return $this->router;
    }

    public function getRequest(): Request
    {
        return $this->request;
    }
} 