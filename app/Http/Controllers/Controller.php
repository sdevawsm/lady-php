<?php

namespace App\Http\Controllers;

use LadyPHP\Http\Request;
use LadyPHP\Http\Response;
use LadyPHP\View\View;

abstract class Controller
{
    /**
     * Inicializa o sistema de views
     */
    protected static function initView(): void
    {
        $viewPath = dirname(__DIR__, 3) . '/resources/views';
        $cachePath = dirname(__DIR__, 3) . '/storage/cache/views';
        View::init($viewPath, $cachePath);
    }

    /**
     * Renderiza uma view
     * 
     * @param string $view Nome da view
     * @param array $data Dados para a view
     * @return Response
     */
    protected function view(string $view, array $data = []): Response
    {
        static::initView();
        error_log("Dados sendo passados para a view: " . print_r($data, true));
        return View::render($view, $data);
    }

    /**
     * Retorna uma resposta JSON
     */
    protected function json($data, int $statusCode = 200): Response
    {
        return Response::json($data, $statusCode);
    }

    /**
     * Retorna uma resposta de texto
     */
    protected function response($content, int $statusCode = 200): Response
    {
        return new Response($content, $statusCode);
    }
} 