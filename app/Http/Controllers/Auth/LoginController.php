<?php

namespace App\Http\Controllers\Auth;

use LadyPHP\Http\Controller;
use LadyPHP\Http\Request;
use LadyPHP\Http\Response;
use LadyPHP\Validation\ValidatorFactory;

class LoginController extends Controller
{
    public function index(): Response
    {
        return new Response('Página de login');
    }

    public function login(Request $request): Response
    {
        $data = $request->all();
        
        // Define as regras de validação
        $rules = [
            'name' => 'required|min:3',
            'email' => 'required|email',
            'password' => 'required|min:6',
            'age' => 'required|numeric|min:18',
            'website' => 'url',
            'phone' => 'required|min:10',
            'interests' => 'required|array|min:1'
        ];

        // Define as mensagens de erro personalizadas
        $messages = [
            'name.required' => 'O nome é obrigatório',
            'name.min' => 'O nome deve ter pelo menos 3 caracteres',
            'email.required' => 'O email é obrigatório',
            'email.email' => 'Digite um email válido',
            'password.required' => 'A senha é obrigatória',
            'password.min' => 'A senha deve ter pelo menos 6 caracteres',
            'age.required' => 'A idade é obrigatória',
            'age.numeric' => 'A idade deve ser um número',
            'age.min' => 'Você deve ter pelo menos 18 anos',
            'website.url' => 'Digite uma URL válida',
            'phone.required' => 'O telefone é obrigatório',
            'phone.min' => 'O telefone deve ter pelo menos 10 dígitos',
            'interests.required' => 'Selecione pelo menos um interesse',
            'interests.array' => 'Os interesses devem ser selecionados corretamente',
            'interests.min' => 'Selecione pelo menos um interesse'
        ];

        // Valida os dados
        $result = ValidatorFactory::validate($data, $rules, $messages);

        // Retorna o resultado em JSON
        return $this->json([
            'success' => $result['success'],
            'data' => $data,
            'errors' => $result['errors'] ?? null
        ], $result['success'] ? 200 : 422);
    }
}