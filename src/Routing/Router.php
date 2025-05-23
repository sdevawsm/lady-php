<?php

namespace LadyPHP\Routing;

use LadyPHP\Http\Request;
use LadyPHP\Http\Response;

/**
 * Classe Router
 * 
 * Gerencia o sistema de rotas da aplicação.
 * Responsável por registrar rotas, agrupá-las e despachar as requisições
 * para as rotas correspondentes.
 */
class Router
{
    /**
     * Lista de todas as rotas registradas
     * 
     * @var array<Route>
     */
    protected array $routes = [];

    /**
     * Atributos do grupo de rotas atual
     * Usado para aplicar prefixos e middlewares em grupos de rotas
     * 
     * @var array
     */
    protected array $currentGroup = [];

    /**
     * Registra uma rota GET
     *
     * @param string $uri URI da rota
     * @param mixed $action Ação a ser executada (closure ou string "Controller@method")
     * @return Route
     */
    public function get(string $uri, $action): Route
    {
        return $this->addRoute('GET', $uri, $action);
    }

    /**
     * Registra uma rota POST
     *
     * @param string $uri URI da rota
     * @param mixed $action Ação a ser executada (closure ou string "Controller@method")
     * @return Route
     */
    public function post(string $uri, $action): Route
    {
        return $this->addRoute('POST', $uri, $action);
    }

    /**
     * Registra uma rota PUT
     *
     * @param string $uri URI da rota
     * @param mixed $action Ação a ser executada (closure ou string "Controller@method")
     * @return Route
     */
    public function put(string $uri, $action): Route
    {
        return $this->addRoute('PUT', $uri, $action);
    }

    /**
     * Registra uma rota DELETE
     *
     * @param string $uri URI da rota
     * @param mixed $action Ação a ser executada (closure ou string "Controller@method")
     * @return Route
     */
    public function delete(string $uri, $action): Route
    {
        return $this->addRoute('DELETE', $uri, $action);
    }

    /**
     * Cria um grupo de rotas
     * 
     * Permite agrupar rotas com atributos comuns como:
     * - prefix: prefixo para todas as URIs do grupo
     * - middleware: middlewares aplicados a todas as rotas do grupo
     *
     * @param array $attributes Atributos do grupo (prefix, middleware, etc)
     * @param callable $callback Função que define as rotas do grupo
     * @return void
     * 
     * @example
     * $router->group(['prefix' => 'admin', 'middleware' => 'auth'], function($router) {
     *     $router->get('/users', 'UserController@index');
     *     $router->get('/settings', 'SettingsController@index');
     * });
     */
    public function group(array $attributes, callable $callback): void
    {
        // Armazena os atributos do grupo atual
        $this->currentGroup = $attributes;
        
        // Executa o callback para registrar as rotas do grupo
        $callback($this);
        
        // Limpa os atributos do grupo após registrar todas as rotas
        $this->currentGroup = [];
    }

    /**
     * Adiciona uma nova rota ao router
     *
     * @param string $method Método HTTP (GET, POST, etc)
     * @param string $uri URI da rota
     * @param mixed $action Ação a ser executada
     * @return Route
     */
    protected function addRoute(string $method, string $uri, $action): Route
    {
        // Cria uma nova instância de Route
        $route = new Route($method, $uri, $action);
        
        // Aplica os atributos do grupo atual à rota
        if (!empty($this->currentGroup)) {
            // Aplica o prefixo se definido
            if (isset($this->currentGroup['prefix'])) {
                $route->prefix($this->currentGroup['prefix']);
            }
            // Aplica os middlewares se definidos
            if (isset($this->currentGroup['middleware'])) {
                $route->middleware($this->currentGroup['middleware']);
            }
        }

        // Adiciona a rota à lista de rotas registradas
        $this->routes[] = $route;
        return $route;
    }

    /**
     * Despacha a requisição para a rota correspondente
     * 
     * Percorre todas as rotas registradas e executa a primeira
     * que corresponder ao método e URI da requisição.
     * Se nenhuma rota for encontrada, retorna uma resposta 404.
     *
     * @param Request $request Requisição atual
     * @return Response
     */
    public function dispatch(Request $request): Response
    {
        $method = $request->getMethod();
        $uri = $request->getPath();

        // Procura por uma rota que corresponda à requisição
        foreach ($this->routes as $route) {
            if ($route->matches($method, $uri)) {
                return $route->run($request);
            }
        }

        // Retorna 404 se nenhuma rota for encontrada
        return new Response('Route not found', 404);
    }
} 