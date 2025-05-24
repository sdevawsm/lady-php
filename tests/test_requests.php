<?php

require_once __DIR__ . '/../vendor/autoload.php';

use LadyPHP\Http\Request;
use LadyPHP\Http\Response;
use LadyPHP\Routing\Router;

// Função auxiliar para simular uma requisição HTTP
function simular_requisicao(string $metodo, string $uri, array $dados = [], array $headers = [], array $query = []): Response {
    // Limpar variáveis globais
    $_SERVER = [];
    $_POST = [];
    $_GET = [];
    
    // Configurar método e URI
    $_SERVER['REQUEST_METHOD'] = $metodo;
    $_SERVER['REQUEST_URI'] = $uri;
    
    // Configurar headers
    foreach ($headers as $chave => $valor) {
        $_SERVER['HTTP_' . strtoupper(str_replace('-', '_', $chave))] = $valor;
    }
    
    // Configurar dados do POST/PUT
    if (in_array($metodo, ['POST', 'PUT'])) {
        $_POST = $dados;
    }
    
    // Configurar query parameters
    $_GET = $query;
    
    // Criar instância do request
    $request = new Request();
    
    // Criar instância do router
    $router = new Router();
    
    // Registrar rotas de teste
    $router->get('/teste', function(Request $request) {
        return new Response(json_encode([
            'mensagem' => 'Teste GET',
            'query' => $request->getQuery(),
            'headers' => $request->headers()
        ]));
    });
    
    $router->post('/teste', function(Request $request) {
        return new Response(json_encode([
            'mensagem' => 'Teste POST',
            'dados' => $request->all(),
            'headers' => $request->headers()
        ]));
    });
    
    $router->put('/teste/{id}', function(Request $request, $id) {
        return new Response(json_encode([
            'mensagem' => 'Teste PUT',
            'id' => $id,
            'dados' => $request->all(),
            'headers' => $request->headers()
        ]));
    });
    
    $router->delete('/teste/{id}', function(Request $request, $id) {
        return new Response(json_encode([
            'mensagem' => 'Teste DELETE',
            'id' => $id,
            'headers' => $request->headers()
        ]));
    });
    
    // Executar a requisição
    return $router->dispatch($request);
}

// Função auxiliar para verificar se o teste passou
function verificar_teste(string $nome, $esperado, $recebido): void {
    echo "Testando {$nome}...\n";
    
    if ($esperado === $recebido) {
        echo "✅ Teste passou!\n";
    } else {
        echo "❌ Teste falhou!\n";
        echo "Esperado: " . print_r($esperado, true) . "\n";
        echo "Recebido: " . print_r($recebido, true) . "\n";
    }
    echo "\n";
}

// Executar testes
echo "Iniciando testes de requests...\n\n";

// Teste 1: GET simples com query parameters
$response = simular_requisicao('GET', '/teste', [], [], ['pagina' => 1, 'limite' => 10]);
$esperado = [
    'mensagem' => 'Teste GET',
    'query' => ['pagina' => 1, 'limite' => 10],
    'headers' => []
];
verificar_teste('GET com query parameters', $esperado, json_decode($response->getContent(), true));

// Teste 2: POST com dados JSON
$response = simular_requisicao(
    'POST',
    '/teste',
    ['nome' => 'Produto Teste', 'preco' => 99.99],
    ['Content-Type' => 'application/json']
);
$esperado = [
    'mensagem' => 'Teste POST',
    'dados' => ['nome' => 'Produto Teste', 'preco' => 99.99],
    'headers' => ['Content-Type' => 'application/json']
];
verificar_teste('POST com dados JSON', $esperado, json_decode($response->getContent(), true));

// Teste 3: PUT com parâmetros de rota e dados
$response = simular_requisicao(
    'PUT',
    '/teste/123',
    ['nome' => 'Produto Atualizado', 'preco' => 149.99],
    ['Content-Type' => 'application/json']
);
$esperado = [
    'mensagem' => 'Teste PUT',
    'id' => '123',
    'dados' => ['nome' => 'Produto Atualizado', 'preco' => 149.99],
    'headers' => ['Content-Type' => 'application/json']
];
verificar_teste('PUT com parâmetros de rota', $esperado, json_decode($response->getContent(), true));

// Teste 4: DELETE com parâmetros de rota
$response = simular_requisicao(
    'DELETE',
    '/teste/123',
    [],
    ['Authorization' => 'Bearer token123']
);
$esperado = [
    'mensagem' => 'Teste DELETE',
    'id' => '123',
    'headers' => ['Authorization' => 'Bearer token123']
];
verificar_teste('DELETE com headers', $esperado, json_decode($response->getContent(), true));

// Teste 5: GET com múltiplos headers
$response = simular_requisicao(
    'GET',
    '/teste',
    [],
    [
        'Accept' => 'application/json',
        'User-Agent' => 'LadyPHP Test',
        'X-Custom-Header' => 'valor-teste'
    ]
);
$esperado = [
    'mensagem' => 'Teste GET',
    'query' => [],
    'headers' => [
        'Accept' => 'application/json',
        'User-Agent' => 'LadyPHP Test',
        'X-Custom-Header' => 'valor-teste'
    ]
];
verificar_teste('GET com múltiplos headers', $esperado, json_decode($response->getContent(), true));

echo "Testes de requests concluídos!\n"; 