<?php

namespace App\Http\Middleware;

use LadyPHP\Http\Middleware\MiddlewareInterface;
use LadyPHP\Http\Request;
use LadyPHP\Http\Response;

/**
 * Middleware de exemplo para autenticação
 */
class AuthMiddleware implements MiddlewareInterface
{
    /**
     * Processa a requisição através do middleware
     *
     * @param Request $request
     * @param callable $next
     * @return Response
     */
    public function handle(Request $request, callable $next): Response
    {
        // Exemplo: Verifica se existe um token de autenticação
        $token = $request->getHeader('Authorization');
        
        if (!$token) {
            return Response::json([
                'error' => 'Token de autenticação não fornecido'
            ], 401);
        }

        // Aqui você pode validar o token, verificar permissões, etc.
        // Por enquanto, apenas um exemplo simples
        if ($token !== 'Bearer valid-token') {
            return Response::json([
                'error' => 'Token inválido'
            ], 401);
        }

        // Se tudo estiver ok, continua para o próximo middleware/rota
        return $next($request);
    }
} 