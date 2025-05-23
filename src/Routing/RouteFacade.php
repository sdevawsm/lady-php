<?php

namespace LadyPHP\Routing;

use LadyPHP\Core\Application;
use LadyPHP\Http\Request;
use LadyPHP\Http\Response;

/**
 * Classe RouteFacade
 * 
 * Facade estática para o sistema de rotas.
 * Permite o uso de métodos estáticos como Route::get(), Route::post(), etc.
 */
class RouteFacade
{
    /**
     * Instância do router
     */
    protected static ?Router $router = null;

    /**
     * Inicializa o router
     * 
     * @return Router
     */
    protected static function getRouter(): Router
    {
        if (static::$router === null) {
            static::$router = Application::getInstance()->getRouter();
        }

        return static::$router;
    }

    /**
     * Registra uma rota GET
     *
     * @param string $uri URI da rota
     * @param mixed $action Ação a ser executada
     * @return RouteInstance
     * 
     * @example
     * Route::get('/users', 'UserController@index');
     * Route::get('/users/{id}', function($id) { ... });
     */
    public static function get(string $uri, $action): RouteInstance
    {
        return static::getRouter()->get($uri, $action);
    }

    /**
     * Registra uma rota POST
     *
     * @param string $uri URI da rota
     * @param mixed $action Ação a ser executada
     * @return RouteInstance
     */
    public static function post(string $uri, $action): RouteInstance
    {
        return static::getRouter()->post($uri, $action);
    }

    /**
     * Registra uma rota PUT
     *
     * @param string $uri URI da rota
     * @param mixed $action Ação a ser executada
     * @return RouteInstance
     */
    public static function put(string $uri, $action): RouteInstance
    {
        return static::getRouter()->put($uri, $action);
    }

    /**
     * Registra uma rota DELETE
     *
     * @param string $uri URI da rota
     * @param mixed $action Ação a ser executada
     * @return RouteInstance
     */
    public static function delete(string $uri, $action): RouteInstance
    {
        return static::getRouter()->delete($uri, $action);
    }

    /**
     * Cria um grupo de rotas
     *
     * @param array $attributes Atributos do grupo
     * @param callable $callback Função que define as rotas do grupo
     * @return void
     * 
     * @example
     * Route::group(['prefix' => 'admin', 'middleware' => 'auth'], function() {
     *     Route::get('/users', 'UserController@index');
     *     Route::get('/settings', 'SettingsController@index');
     * });
     */
    public static function group(array $attributes, callable $callback): void
    {
        static::getRouter()->group($attributes, $callback);
    }

    /**
     * Registra uma rota para qualquer método HTTP
     *
     * @param string $uri URI da rota
     * @param mixed $action Ação a ser executada
     * @return RouteInstance
     */
    public static function any(string $uri, $action): RouteInstance
    {
        $route = static::getRouter()->get($uri, $action);
        static::getRouter()->post($uri, $action);
        static::getRouter()->put($uri, $action);
        static::getRouter()->delete($uri, $action);
        return $route;
    }

    /**
     * Registra uma rota para múltiplos métodos HTTP
     *
     * @param array $methods Métodos HTTP permitidos
     * @param string $uri URI da rota
     * @param mixed $action Ação a ser executada
     * @return RouteInstance
     * 
     * @example
     * Route::match(['GET', 'POST'], '/users', 'UserController@store');
     */
    public static function match(array $methods, string $uri, $action): RouteInstance
    {
        $route = null;
        foreach ($methods as $method) {
            $method = strtolower($method);
            if (method_exists(static::getRouter(), $method)) {
                $route = static::getRouter()->$method($uri, $action);
            }
        }
        return $route;
    }
} 