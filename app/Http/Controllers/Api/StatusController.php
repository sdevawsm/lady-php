<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use LadyPHP\Http\Request;
use LadyPHP\Http\Response;

class StatusController extends Controller
{
    /**
     * Retorna o status da API
     */
    public function index(): Response
    {
        return $this->json([
            'status' => 'online',
            'version' => '1.0.0',
            'timestamp' => date('Y-m-d H:i:s')
        ]);
    }

    /**
     * Retorna informações detalhadas do sistema
     */
    public function details(): Response
    {
        return $this->json([
            'status' => 'online',
            'version' => '1.0.0',
            'timestamp' => date('Y-m-d H:i:s'),
            'environment' => 'development',
            'php_version' => PHP_VERSION,
            'server' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown'
        ]);
    }
} 