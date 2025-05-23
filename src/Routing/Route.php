<?php

namespace LadyPHP\Routing;

use LadyPHP\Http\Request;
use LadyPHP\Http\Response;

/**
 * Classe Route
 * 
 * Representa uma rota individual no sistema de roteamento.
 * Responsável por armazenar as informações da rota e processar as requisições.
 */
class Route
{
    /**
     * Método HTTP da rota (GET, POST, PUT, DELETE, etc)
     */
    protected string $method;

    /**
     * URI da rota (ex: /users/{id})
     */
    protected string $uri;

    /**
     * Ação a ser executada quando a rota for acessada.
     * Pode ser uma closure ou uma string no formato "Controller@method"
     */
    protected $action;

    /**
     * Lista de middlewares que serão executados antes da ação da rota
     */
    protected array $middleware = [];

    /**
     * Prefixo opcional para a URI da rota
     * Útil quando a rota faz parte de um grupo
     */
    protected ?string $prefix = null;

    /**
     * Construtor da rota
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
     * Adiciona middlewares à rota
     *
     * @param string|array $middleware Middleware(s) a ser(em) adicionado(s)
     * @return self
     */
    public function middleware($middleware): self
    {
        $this->middleware = array_merge(
            $this->middleware,
            is_array($middleware) ? $middleware : [$middleware]
        );
        return $this;
    }

    /**
     * Define um prefixo para a URI da rota
     *
     * @param string $prefix Prefixo a ser adicionado
     * @return self
     */
    public function prefix(string $prefix): self
    {
        $this->prefix = $prefix;
        return $this;
    }

    /**
     * Retorna a URI completa da rota, incluindo o prefixo se existir
     *
     * @return string
     */
    public function getUri(): string
    {
        return $this->prefix ? $this->prefix . $this->uri : $this->uri;
    }

    /**
     * Verifica se a rota corresponde ao método e URI fornecidos
     *
     * @param string $method Método HTTP da requisição
     * @param string $uri URI da requisição
     * @return bool
     */
    public function matches(string $method, string $uri): bool
    {
        // Verifica se o método HTTP corresponde
        if ($this->method !== $method) {
            return false;
        }

        // Verifica se a URI corresponde ao padrão da rota
        $pattern = $this->getPattern();
        return (bool) preg_match($pattern, $uri);
    }

    /**
     * Converte a URI da rota em um padrão regex para matching
     * Ex: /users/{id} -> #^/users/(?P<id>[^/]+)$#
     *
     * @return string
     */
    protected function getPattern(): string
    {
        $pattern = $this->getUri();
        // Converte parâmetros de rota em grupos de captura regex
        // Ex: {id} -> (?P<id>[^/]+)
        $pattern = preg_replace('/\{([a-zA-Z]+)\}/', '(?P<$1>[^/]+)', $pattern);
        return '#^' . $pattern . '$#';
    }

    /**
     * Executa a rota, processando middlewares e a ação
     *
     * @param Request $request Requisição atual
     * @return Response
     * @throws \RuntimeException Se a ação da rota for inválida
     */
    public function run(Request $request): Response
    {
        // Executa cada middleware na ordem
        foreach ($this->middleware as $middleware) {
            $response = $middleware->handle($request);
            // Se o middleware retornar uma resposta, interrompe a execução
            if ($response instanceof Response) {
                return $response;
            }
        }

        // Executa a ação da rota
        if (is_callable($this->action)) {
            // Se for uma closure, executa diretamente
            return call_user_func($this->action, $request);
        }

        if (is_string($this->action)) {
            // Se for uma string no formato "Controller@method"
            [$controller, $method] = explode('@', $this->action);
            $controller = new $controller();
            return $controller->$method($request);
        }

        throw new \RuntimeException('Invalid route action');
    }
} 