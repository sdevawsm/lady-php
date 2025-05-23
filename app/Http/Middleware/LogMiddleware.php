<?php

namespace App\Http\Middleware;

use LadyPHP\Http\Middleware\MiddlewareInterface;
use LadyPHP\Http\Request;
use LadyPHP\Http\Response;

/**
 * Middleware para logging de requisições
 * Registra informações sobre cada requisição recebida
 */
class LogMiddleware implements MiddlewareInterface
{
    /**
     * Processa a requisição através do middleware
     */
    public function handle(Request $request, callable $next): Response
    {
        // Registra o início da requisição
        $startTime = microtime(true);
        $method = $request->getMethod();
        $uri = $request->getPath();
        
        error_log(sprintf(
            "[%s] Iniciando requisição: %s %s",
            date('Y-m-d H:i:s'),
            $method,
            $uri
        ));

        // Processa a requisição
        $response = $next($request);

        // Registra o fim da requisição
        $duration = microtime(true) - $startTime;
        error_log(sprintf(
            "[%s] Requisição finalizada: %s %s - Status: %d - Duração: %.2fms",
            date('Y-m-d H:i:s'),
            $method,
            $uri,
            $response->getStatusCode(),
            $duration * 1000
        ));

        return $response;
    }
} 