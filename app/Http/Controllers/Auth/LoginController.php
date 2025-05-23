<?php

namespace App\Http\Controllers\Auth;

use LadyPHP\Http\Controller;
use LadyPHP\Http\Request;
use LadyPHP\Http\Response;

class LoginController extends Controller
{
    public function index(): Response
    {
        return new Response('Página de login');
    }

    public function login(Request $request): Response
    {
        // Usando dd() como no Laravel, sem necessidade de importação
        dd([
            'Método HTTP' => $request->method(),
            'URI Completa' => $request->getUri(),
            'Caminho' => $request->getPath(),
            'Headers' => $request->headers(),
            'Content Type' => $request->header('Content-Type'),
            'É JSON?' => $request->isJson(),
            'Dados GET' => $request->get(),
            'Dados POST' => $request->post(),
            'Dados JSON' => $request->json(),
            'Todos os Dados' => $request->all(),
            'Dados do Servidor' => [
                'REQUEST_METHOD' => $request->server['REQUEST_METHOD'] ?? null,
                'REQUEST_URI' => $request->server['REQUEST_URI'] ?? null,
                'HTTP_HOST' => $request->server['HTTP_HOST'] ?? null,
                'REMOTE_ADDR' => $request->server['REMOTE_ADDR'] ?? null,
                'HTTP_USER_AGENT' => $request->server['HTTP_USER_AGENT'] ?? null,
            ],
            'Cookies' => $request->cookie(),
            'Arquivos' => $request->file()
        ]);

        // Exemplos de uso dos novos métodos:
        // dd($request->cookie('session'));
        // dd($request->cookies());
        // dd($request->hasFile('avatar'));
        // dd($request->file('avatar'));
    }
}