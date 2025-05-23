<?php

namespace App\Http\Middleware;

use LadyPHP\Http\Request;
use LadyPHP\Http\Response;

interface Middleware
{
    /**
     * Processa a requisição através do middleware
     *
     * @param Request $request
     * @param callable $next
     * @return Response|null
     */
    public function handle(Request $request, callable $next): ?Response;
} 