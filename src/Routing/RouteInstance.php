<?php

namespace LadyPHP\Routing;

use LadyPHP\Http\Request;
use LadyPHP\Http\Response;
use LadyPHP\Http\Middleware\Pipeline;

/**
 * Classe RouteInstance
 * 
 * Representa uma rota individual no sistema.
 * Responsável por armazenar os detalhes da rota e executá-la quando correspondida.
 */
class RouteInstance
{
    /**
     * Método HTTP da rota
     */
    protected string $method;

    /**
     * URI da rota
     */
    protected string $uri;

    /**
     * Ação a ser executada (closure ou string "Controller@method")
     */
    protected $action;

    /**
     * Prefixo da rota (usado em grupos)
     */
    protected ?string $prefix = null;

    /**
     * Middlewares da rota
     */
    protected array $middlewares = [];

    /**
     * Construtor
     *
     * @param string $method Método HTTP
     * @param string $uri URI da rota
     * @param mixed $action Ação a ser executada
     */
    public function __construct(string $method, string $uri, $action)
    {
        $this->method = $method;
        $this->uri = $uri;
        $this->action = $action;
    }

    /**
     * Define o prefixo da rota
     *
     * @param string $prefix
     * @return self
     */
    public function prefix(string $prefix): self
    {
        $this->prefix = $prefix;
        return $this;
    }

    /**
     * Define os middlewares da rota
     *
     * @param string|array $middleware
     * @return self
     */
    public function middleware($middleware): self
    {
        $this->middlewares = array_merge(
            $this->middlewares,
            is_array($middleware) ? $middleware : [$middleware]
        );
        return $this;
    }

    /**
     * Verifica se a rota corresponde à requisição
     *
     * @param string $method Método HTTP da requisição
     * @param string $uri URI da requisição
     * @return bool
     */
    public function matches(string $method, string $uri): bool
    {
        if ($this->method !== $method) {
            error_log("Método não corresponde: {$this->method} !== {$method}");
            return false;
        }

        // Se a URI começa com /api, remove para o matching
        if (strpos($uri, '/api') === 0) {
            $uri = substr($uri, 4); // Remove '/api'
        }

        $pattern = $this->getPattern();
        $matches = (bool) preg_match($pattern, $uri);
        
        error_log(sprintf(
            "Tentando match - URI: %s, Pattern: %s, Resultado: %s",
            $uri,
            $pattern,
            $matches ? 'true' : 'false'
        ));

        return $matches;
    }

    /**
     * Executa a rota
     *
     * @param Request $request
     * @return Response
     */
    public function run(Request $request): Response
    {
        // Cria o pipeline de middlewares
        $pipeline = new Pipeline();

        // Adiciona os middlewares da rota ao pipeline
        foreach ($this->middlewares as $middleware) {
            $pipeline->pipe($middleware);
        }

        // Define a ação final (a rota em si)
        $destination = function ($request) {
            if (is_callable($this->action)) {
                return call_user_func($this->action, $request);
            }

            // TODO: Implementar execução de controller
            return new Response('Not implemented', 501);
        };

        // Executa o pipeline
        return $pipeline->process($request, $destination);
    }

    /**
     * Retorna o padrão regex para matching da URI
     *
     * @return string
     */
    protected function getPattern(): string
    {
        $uri = $this->uri;
        
        // Adiciona o prefixo se existir
        if ($this->prefix !== null) {
            // Garante que o prefixo começa com / e a URI não começa com /
            $prefix = '/' . ltrim($this->prefix, '/');
            $uri = rtrim($prefix, '/') . '/' . ltrim($uri, '/');
        }

        // Garante que a URI começa com /
        $uri = '/' . ltrim($uri, '/');

        // Se a URI começa com /api, remove para o matching
        if (strpos($uri, '/api') === 0) {
            $uri = substr($uri, 4); // Remove '/api'
        }

        // Converte parâmetros de rota em padrões regex
        $pattern = preg_replace('/\{([a-zA-Z]+)\}/', '(?P<$1>[^/]+)', $uri);
        return '#^' . $pattern . '$#';
    }

    /**
     * Retorna o método HTTP da rota
     */
    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * Retorna a URI da rota
     */
    public function getUri(): string
    {
        return $this->uri;
    }

    /**
     * Retorna o prefixo da rota
     */
    public function getPrefix(): ?string
    {
        return $this->prefix;
    }
} 