<?php

namespace App\Http\Controllers;

use LadyPHP\Http\Request;
use LadyPHP\Http\Response;

abstract class Controller
{
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