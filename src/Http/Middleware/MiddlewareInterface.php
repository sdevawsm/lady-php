<?php

namespace LadyPHP\Http\Middleware;

use LadyPHP\Http\Request;
use LadyPHP\Http\Response;

/**
 * Interface MiddlewareInterface
 * 
 * Define o contrato que todos os middlewares devem seguir.
 * Um middleware pode processar a requisição antes e/ou depois da rota.
 */
interface MiddlewareInterface
{
    /**
     * Processa a requisição através do middleware
     *
     * @param Request $request A requisição atual
     * @param callable $next A próxima função do pipeline
     * @return Response
     */
    public function handle(Request $request, callable $next): Response;
} 