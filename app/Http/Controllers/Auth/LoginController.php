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
        // Exemplo 1: Usando dd() para debug e encerrar
        // $request->dd($request->toArray());
        
        // Exemplo 2: Usando dump() para debug sem encerrar
        // $request->dump($request->toArray());
        
        // Exemplo 3: Retornando todos os dados como JSON
        return $this->json($request->toArray());
        
        // Exemplo 4: Debug específico
        // $request->dd(
        //     $request->method(),
        //     $request->all(),
        //     $request->json(),
        //     $request->headers()
        // );
    }
}