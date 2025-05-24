<?php

namespace LadyPHP\Routing;

use LadyPHP\Http\Request;
use LadyPHP\Http\Response;
use LadyPHP\Http\Middleware\MiddlewareInterface;

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
     * @var array<RouteInstance>
     */
    protected array $routes = [];

    /**
     * Pilha de grupos de rotas atuais
     * Usado para aplicar prefixos e middlewares em grupos aninhados
     * 
     * @var array
     */
    protected array $groupStack = [];

    /**
     * Registra uma rota GET
     *
     * @param string $uri URI da rota
     * @param mixed $action Ação a ser executada (closure ou string "Controller@method")
     * @return RouteInstance
     */
    public function get(string $uri, $action): RouteInstance
    {
        return $this->addRoute('GET', $uri, $action);
    }

    /**
     * Registra uma rota POST
     *
     * @param string $uri URI da rota
     * @param mixed $action Ação a ser executada (closure ou string "Controller@method")
     * @return RouteInstance
     */
    public function post(string $uri, $action): RouteInstance
    {
        return $this->addRoute('POST', $uri, $action);
    }

    /**
     * Registra uma rota PUT
     *
     * @param string $uri URI da rota
     * @param mixed $action Ação a ser executada (closure ou string "Controller@method")
     * @return RouteInstance
     */
    public function put(string $uri, $action): RouteInstance
    {
        return $this->addRoute('PUT', $uri, $action);
    }

    /**
     * Registra uma rota DELETE
     *
     * @param string $uri URI da rota
     * @param mixed $action Ação a ser executada (closure ou string "Controller@method")
     * @return RouteInstance
     */
    public function delete(string $uri, $action): RouteInstance
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
        // Adiciona os atributos do grupo à pilha
        $this->groupStack[] = $attributes;
        
        // Executa o callback para registrar as rotas do grupo
        $callback($this);
        
        // Remove os atributos do grupo da pilha
        array_pop($this->groupStack);
    }

    /**
     * Adiciona uma nova rota ao router
     *
     * @param string $method Método HTTP (GET, POST, etc)
     * @param string $uri URI da rota
     * @param mixed $action Ação a ser executada
     * @return RouteInstance
     */
    protected function addRoute(string $method, string $uri, $action): RouteInstance
    {
        // Cria uma nova instância de RouteInstance
        $route = new RouteInstance($method, $uri, $action);
        
        // Aplica os atributos de todos os grupos na pilha
        foreach ($this->groupStack as $group) {
            // Aplica o prefixo se definido
            if (isset($group['prefix'])) {
                $currentPrefix = $route->getPrefix();
                $newPrefix = $group['prefix'];
                
                // Combina os prefixos se já existir um
                if ($currentPrefix !== null) {
                    $newPrefix = rtrim($currentPrefix, '/') . '/' . ltrim($newPrefix, '/');
                }
                
                $route->prefix($newPrefix);
            }
            
            // Aplica os middlewares se definidos
            if (isset($group['middleware'])) {
                $route->middleware($group['middleware']);
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
     * @throws \Exception Se a rota não for encontrada
     */
    public function dispatch(Request $request): Response
    {
        $method = $request->getMethod();
        $uri = $request->getPathInfo();

        // Debug: Mostrar rotas registradas
        error_log("Rotas registradas:");
        foreach ($this->routes as $route) {
            error_log(sprintf(
                "Método: %s, URI: %s, Prefixo: %s",
                $route->getMethod(),
                $route->getUri(),
                $route->getPrefix() ?? 'nenhum'
            ));
        }
        error_log("Requisição atual - Método: {$method}, URI: {$uri}");

        // Procura por uma rota que corresponda à requisição
        foreach ($this->routes as $route) {
            if ($route->matches($method, $uri)) {
                return $route->run($request);
            }
        }

        // Se nenhuma rota foi encontrada, lança exceção
        throw new \Exception("Route not found: {$method} {$uri}");
    }
} 