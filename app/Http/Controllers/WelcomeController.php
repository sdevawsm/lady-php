<?php

namespace App\Http\Controllers;

use LadyPHP\Http\Response;

class WelcomeController extends Controller
{
    public function index(): Response
    {
        return $this->view('welcome', [
            'message' => 'Bem-vindo ao seu novo projeto!',
            'features' => [
                'Sistema de Rotas Simples',
                'Validação de Dados',
                'Sistema de Views (Elle)',
                'Controladores HTTP',
                'Respostas JSON'
            ],
            'showExtra' => true,
            'extraInfo' => 'Este é um framework PHP minimalista e elegante, inspirado em Laravel mas com uma abordagem mais simples.',
            'year' => date('Y')
        ]);
    }
} 