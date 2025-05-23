<?php

namespace App\Http\Controllers\Admin;

use LadyPHP\Http\Controller;
use LadyPHP\Http\Request;
use LadyPHP\Http\Response;

class DashboardController extends Controller
{
    /**
     * Retorna o painel administrativo
     */
    public function index(): Response
    {
        return $this->json([
            'message' => 'Painel Administrativo',
            'user' => 'admin',
            'menu' => [
                'dashboard' => '/admin',
                'users' => '/admin/users',
                'settings' => '/admin/settings'
            ]
        ]);
    }

    /**
     * Retorna a lista de usuários
     */
    public function users(): Response
    {
        return $this->json([
            'users' => [
                ['id' => 1, 'name' => 'Admin', 'role' => 'admin'],
                ['id' => 2, 'name' => 'User 1', 'role' => 'user'],
                ['id' => 3, 'name' => 'User 2', 'role' => 'user']
            ]
        ]);
    }

    /**
     * Retorna as configurações do sistema
     */
    public function settings(): Response
    {
        return $this->json([
            'settings' => [
                'site_name' => 'LadyPHP Framework',
                'maintenance_mode' => false,
                'debug_mode' => true
            ]
        ]);
    }
} 