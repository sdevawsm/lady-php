<?php

namespace LadyPHP\Http\Middleware;

use LadyPHP\Http\Request;
use LadyPHP\Http\Response;

class BasicMiddleware implements MiddlewareInterface
{
    public function handle(Request $request, callable $next): Response
    {
        // Simplesmente passa a requisição para o próximo middleware
        return $next($request);
    }
} 