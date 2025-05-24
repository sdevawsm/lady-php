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
     * Lista de middlewares no pipeline
     */
    protected array $pipes = [];

    /**
     * Adiciona um middleware ao pipeline
     *
     * @param string|MiddlewareInterface $middleware
     * @return self
     */
    public function pipe($middleware): self
    {
        // Se for uma string (nome da classe), instancia o middleware
        if (is_string($middleware)) {
            if (!class_exists($middleware)) {
                throw new \Exception("Middleware class {$middleware} not found");
            }
            $middleware = new $middleware();
        }

        // Verifica se o middleware implementa a interface
        if (!$middleware instanceof MiddlewareInterface) {
            throw new \Exception("Middleware deve implementar LadyPHP\\Http\\Middleware\\MiddlewareInterface");
        }

        $this->pipes[] = $middleware;
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
        // Se não houver middlewares, executa o destino diretamente
        if (empty($this->pipes)) {
            return $destination($request);
        }

        // Cria o closure que será executado pelo middleware
        $next = function (Request $request) use ($destination) {
            return $destination($request);
        };

        // Executa os middlewares em ordem reversa
        foreach (array_reverse($this->pipes) as $pipe) {
            $next = function (Request $request) use ($pipe, $next) {
                return $pipe->handle($request, $next);
            };
        }

        // Executa o primeiro middleware
        return $next($request);
    }
} 