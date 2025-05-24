<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Models\Category;
use App\Models\Product;
use LadyPHP\Database\Model;
use LadyPHP\Http\Request;
use LadyPHP\Http\Response;
use LadyPHP\Routing\Router;
use LadyPHP\Http\Route;

// Função auxiliar para formatar números no JSON
function format_json_numbers($data) {
    if (is_array($data)) {
        foreach ($data as $key => $value) {
            $data[$key] = format_json_numbers($value);
        }
        return $data;
    }
    if (is_float($data)) {
        // Converter para string com 2 casas decimais
        return number_format($data, 2, '.', '');
    }
    return $data;
}

// Função auxiliar para normalizar valores JSON
function normalize_json_value($value) {
    if (is_string($value) && is_numeric($value)) {
        // Se for uma string numérica, converte para float
        return (float)$value;
    }
    if (is_array($value)) {
        foreach ($value as $key => $val) {
            $value[$key] = normalize_json_value($val);
        }
    }
    return $value;
}

// Função auxiliar para formatar a saída JSON
function format_json_output($json) {
    if (is_array($json)) {
        foreach ($json as $key => $value) {
            $json[$key] = format_json_output($value);
        }
        return $json;
    }
    if (is_float($json)) {
        // Formata o número para exibição com 2 casas decimais
        return number_format($json, 2, '.', '');
    }
    return $json;
}

// Função auxiliar para normalizar string de resposta
function normalize_response($response, $for_comparison = true) {
    // Se a resposta contém JSON
    if (strpos($response, '{') !== false) {
        // Extrai o prefixo (ex: "Produto criado: ")
        $prefix = substr($response, 0, strpos($response, '{'));
        
        // Extrai o JSON
        $jsonStr = substr($response, strpos($response, '{'));
        
        // Decodifica o JSON
        $json = json_decode($jsonStr, true);
        
        if ($for_comparison) {
            // Para comparação: normaliza os valores
            $json = normalize_json_value($json);
        } else {
            // Para exibição: formata os números
            $json = format_json_output($json);
        }
        
        // Recodifica o JSON
        $normalizedJson = json_encode($json, JSON_PRESERVE_ZERO_FRACTION | JSON_UNESCAPED_UNICODE);
        
        // Retorna a string normalizada
        return $prefix . $normalizedJson;
    }
    return $response;
}

// Criar instância do Router
$router = new Router();

// Redirecionar error_log para um arquivo temporário durante os testes
$temp_log_file = tempnam(sys_get_temp_dir(), 'lady_php_test_');
$original_error_log = ini_get('error_log');
ini_set('error_log', $temp_log_file);

// Desabilitar logs de debug temporariamente
$original_error_reporting = error_reporting();
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING & ~E_DEPRECATED);

// Registrar rotas
$router->get('/', function() {
    return new Response('Página inicial');
});

$router->get('/produtos', function() {
    return new Response('Lista de produtos');
});

$router->get('/produtos/{id}', function(Request $request, $id) {
    return new Response("Detalhes do produto {$id}");
});

$router->post('/produtos', function(Request $request) {
    $data = $request->all();
    // Formata os números antes de codificar em JSON
    $data = format_json_numbers($data);
    // Força a formatação do JSON para usar 2 casas decimais
    return new Response("Produto criado: " . json_encode($data, JSON_PRESERVE_ZERO_FRACTION | JSON_UNESCAPED_UNICODE));
});

$router->put('/produtos/{id}', function(Request $request, $id) {
    $data = $request->all();
    // Formata os números antes de codificar em JSON
    $data = format_json_numbers($data);
    // Força a formatação do JSON para usar 2 casas decimais
    return new Response("Produto {$id} atualizado: " . json_encode($data, JSON_PRESERVE_ZERO_FRACTION | JSON_UNESCAPED_UNICODE));
});

$router->delete('/produtos/{id}', function(Request $request, $id) {
    return new Response("Produto {$id} excluído");
});

// Testar rotas
echo "Testando rotas...\n\n";

// Simular diferentes requisições
$tests = [
    // GET /
    [
        'method' => 'GET',
        'uri' => '/',
        'expected' => 'Página inicial'
    ],
    // GET /produtos
    [
        'method' => 'GET',
        'uri' => '/produtos',
        'expected' => 'Lista de produtos'
    ],
    // GET /produtos/1
    [
        'method' => 'GET',
        'uri' => '/produtos/1',
        'expected' => 'Detalhes do produto 1'
    ],
    // POST /produtos
    [
        'method' => 'POST',
        'uri' => '/produtos',
        'body' => ['name' => 'Smartphone', 'price' => 1999.99],
        'expected' => 'Produto criado: {"name":"Smartphone","price":1999.99}'
    ],
    // PUT /produtos/1
    [
        'method' => 'PUT',
        'uri' => '/produtos/1',
        'body' => ['price' => 2999.99],
        'expected' => 'Produto 1 atualizado: {"price":2999.99}'
    ],
    // DELETE /produtos/1
    [
        'method' => 'DELETE',
        'uri' => '/produtos/1',
        'expected' => 'Produto 1 excluído'
    ]
];

foreach ($tests as $test) {
    echo "Testando {$test['method']} {$test['uri']}...\n";
    
    // Limpar variáveis globais antes de cada teste
    $_SERVER = [];
    $_POST = [];
    
    // Criar request simulada
    $_SERVER['REQUEST_METHOD'] = $test['method'];
    $_SERVER['REQUEST_URI'] = $test['uri'];
    if (isset($test['body'])) {
        $_POST = $test['body'];
    }
    
    try {
        $request = new Request();
        $response = $router->dispatch($request);
        
        // Normalizar a saída para comparação
        $expected = normalize_response(trim($test['expected']), true);
        $received = normalize_response(trim($response->getContent()), true);
        
        if ($expected === $received) {
            // Para exibição, formata os números com 2 casas decimais
            $formattedReceived = normalize_response(trim($response->getContent()), false);
            echo "✅ Passou: {$formattedReceived}\n\n";
        } else {
            echo "❌ Falhou:\n";
            echo "Esperado: " . normalize_response(trim($test['expected']), false) . "\n";
            echo "Recebido: " . normalize_response(trim($response->getContent()), false) . "\n\n";
        }
    } catch (\Exception $e) {
        echo "❌ Erro: " . $e->getMessage() . "\n\n";
    }
}

// Restaurar configurações originais
ini_set('error_log', $original_error_log);
error_reporting($original_error_reporting);

// Limpar arquivo de log temporário
if (file_exists($temp_log_file)) {
    unlink($temp_log_file);
}

// Testar rota não encontrada
echo "Testando rota não encontrada...\n";

// Redirecionar error_log novamente para o arquivo temporário
$temp_log_file = tempnam(sys_get_temp_dir(), 'lady_php_test_');
ini_set('error_log', $temp_log_file);

$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['REQUEST_URI'] = '/nao-existe';

try {
    $request = new Request();
    $router->dispatch($request);
    echo "❌ Falhou: Deveria ter lançado exceção para rota não encontrada\n";
} catch (\Exception $e) {
    echo "✅ Passou: " . $e->getMessage() . "\n";
}

// Restaurar configurações originais e limpar arquivo de log
ini_set('error_log', $original_error_log);
if (file_exists($temp_log_file)) {
    unlink($temp_log_file);
} 