<?php

namespace LadyPHP\Http\Middleware;

use LadyPHP\Http\Request;
use LadyPHP\Http\Response;

/**
 * Classe Pipeline
 * 
 * Gerencia o pipeline de middlewares.
 * Permite que os middlewares sejam executados em sequência,
 * com a possibilidade de interromper a execução a qualquer momento.
 */
class Pipeline
{
    /**
     * Lista de middlewares a serem executados
     */
    protected array $middlewares = [];

    /**
     * Adiciona um middleware ao pipeline
     *
     * @param string|MiddlewareInterface $middleware
     * @return self
     */
    public function pipe($middleware): self
    {
        $this->middlewares[] = $middleware;
        return $this;
    }

    /**
     * Executa o pipeline de middlewares
     *
     * @param Request $request
     * @param callable $destination Função final a ser executada (geralmente a rota)
     * @return Response
     */
    public function process(Request $request, callable $destination): Response
    {
        // Cria o pipeline reverso (último middleware é executado primeiro)
        $pipeline = array_reduce(
            array_reverse($this->middlewares),
            function ($next, $middleware) {
                return function ($request) use ($next, $middleware) {
                    // Se o middleware for uma string (nome da classe), instancia ele
                    if (is_string($middleware)) {
                        $middleware = new $middleware();
                    }

                    // Verifica se o middleware implementa a interface
                    if (!$middleware instanceof MiddlewareInterface) {
                        throw new \RuntimeException(
                            'Middleware deve implementar ' . MiddlewareInterface::class
                        );
                    }

                    return $middleware->handle($request, $next);
                };
            },
            $destination
        );

        // Executa o pipeline
        return $pipeline($request);
    }
} 