<?php

require_once __DIR__ . '/../vendor/autoload.php';

use LadyPHP\Http\Response;

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

// Função auxiliar para extrair headers da resposta
function extrair_headers(Response $response): array {
    $headers = [];
    foreach ($response->getHeaders() as $chave => $valor) {
        $headers[strtolower($chave)] = $valor;
    }
    return $headers;
}

echo "Iniciando testes de Response...\n\n";

// Teste 1: Resposta básica com texto
$response = new Response('Olá, mundo!');
verificar_teste(
    'Resposta básica com texto',
    'Olá, mundo!',
    $response->getContent()
);

// Teste 2: Resposta JSON
$dados = ['mensagem' => 'Sucesso', 'dados' => ['id' => 1, 'nome' => 'Teste']];
$response = new Response(json_encode($dados));
$response->setHeader('Content-Type', 'application/json');
verificar_teste(
    'Resposta JSON',
    [
        'content' => json_encode($dados),
        'headers' => ['content-type' => 'application/json']
    ],
    [
        'content' => $response->getContent(),
        'headers' => extrair_headers($response)
    ]
);

// Teste 3: Resposta com código de status
$response = new Response('Não encontrado', 404);
verificar_teste(
    'Resposta com código 404',
    [
        'content' => 'Não encontrado',
        'status' => 404
    ],
    [
        'content' => $response->getContent(),
        'status' => $response->getStatusCode()
    ]
);

// Teste 4: Resposta com múltiplos headers
$response = new Response('Conteúdo protegido');
$response->setHeader('Content-Type', 'text/plain');
$response->setHeader('X-Custom-Header', 'valor-teste');
$response->setHeader('Cache-Control', 'no-cache');
verificar_teste(
    'Resposta com múltiplos headers',
    [
        'content-type' => 'text/plain',
        'x-custom-header' => 'valor-teste',
        'cache-control' => 'no-cache'
    ],
    extrair_headers($response)
);

// Teste 5: Resposta com redirecionamento
$response = new Response('', 302);
$response->setHeader('Location', '/nova-pagina');
verificar_teste(
    'Resposta com redirecionamento',
    [
        'status' => 302,
        'headers' => ['location' => '/nova-pagina']
    ],
    [
        'status' => $response->getStatusCode(),
        'headers' => extrair_headers($response)
    ]
);

// Teste 6: Resposta com cookie
$response = new Response('Conteúdo com cookie');
$response->cookie('sessao', 'abc123', 3600, '/', '', true, true);
verificar_teste(
    'Resposta com cookie',
    [
        'content' => 'Conteúdo com cookie',
        'headers' => [
            'content-type' => 'text/html; charset=UTF-8',
            'set-cookie' => ['sessao=abc123; Max-Age=3600; Path=/; Secure; HttpOnly']
        ]
    ],
    [
        'content' => $response->getContent(),
        'headers' => extrair_headers($response)
    ]
);

// Teste 7: Resposta com HTML
$html = '<!DOCTYPE html><html><body><h1>Página de Teste</h1></body></html>';
$response = new Response($html);
$response->setHeader('Content-Type', 'text/html; charset=UTF-8');
verificar_teste(
    'Resposta com HTML',
    [
        'content' => $html,
        'headers' => ['content-type' => 'text/html; charset=UTF-8']
    ],
    [
        'content' => $response->getContent(),
        'headers' => extrair_headers($response)
    ]
);

// Teste 8: Resposta com dados binários (simulado)
$dados_binarios = base64_encode('dados binários simulados');
$response = new Response($dados_binarios);
$response->setHeader('Content-Type', 'application/octet-stream');
$response->setHeader('Content-Disposition', 'attachment; filename="arquivo.bin"');
verificar_teste(
    'Resposta com dados binários',
    [
        'content' => $dados_binarios,
        'headers' => [
            'content-type' => 'application/octet-stream',
            'content-disposition' => 'attachment; filename="arquivo.bin"'
        ]
    ],
    [
        'content' => $response->getContent(),
        'headers' => extrair_headers($response)
    ]
);

// Teste 9: Resposta com erro
$response = new Response(json_encode(['erro' => 'Acesso negado']), 403);
$response->setHeader('Content-Type', 'application/json');
verificar_teste(
    'Resposta com erro 403',
    [
        'content' => json_encode(['erro' => 'Acesso negado']),
        'status' => 403,
        'headers' => ['content-type' => 'application/json']
    ],
    [
        'content' => $response->getContent(),
        'status' => $response->getStatusCode(),
        'headers' => extrair_headers($response)
    ]
);

// Teste 10: Resposta vazia
$response = new Response('', 204);
verificar_teste(
    'Resposta vazia (204 No Content)',
    [
        'content' => '',
        'status' => 204
    ],
    [
        'content' => $response->getContent(),
        'status' => $response->getStatusCode()
    ]
);

echo "Testes de Response concluídos!\n"; 