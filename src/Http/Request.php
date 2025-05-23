<?php

namespace LadyPHP\Http;

/**
 * Classe Request
 * 
 * Representa uma requisição HTTP e fornece métodos para acessar
 * dados da requisição de forma segura e organizada.
 * 
 * Esta classe encapsula as superglobais do PHP ($_GET, $_POST, etc)
 * e fornece uma interface limpa para acessar os dados da requisição.
 * 
 * Mantém compatibilidade com os métodos do Laravel para facilitar
 * a migração e uso do framework.
 */
class Request
{
    /**
     * Dados do servidor ($_SERVER)
     * Contém informações sobre o servidor e o ambiente
     */
    protected array $server;

    /**
     * Parâmetros GET ($_GET)
     * Contém os parâmetros da query string
     */
    protected array $get;

    /**
     * Dados POST ($_POST)
     * Contém os dados enviados via POST
     */
    protected array $post;

    /**
     * Arquivos enviados ($_FILES)
     * Contém informações sobre arquivos enviados via upload
     */
    protected array $files;

    /**
     * Cookies ($_COOKIE)
     * Contém os cookies da requisição
     */
    protected array $cookies;

    /**
     * Headers HTTP
     * Contém os headers da requisição
     */
    protected array $headers;

    /**
     * Construtor da classe
     * 
     * Inicializa a requisição capturando todos os dados
     * das superglobais do PHP
     */
    public function __construct()
    {
        $this->server = $_SERVER;
        $this->get = $_GET;
        $this->post = $_POST;
        $this->files = $_FILES;
        $this->cookies = $_COOKIE;
        $this->headers = $this->getHeaders();
    }

    /**
     * Retorna o método HTTP da requisição
     * 
     * @return string Método HTTP em maiúsculas (GET, POST, PUT, DELETE, etc)
     */
    public function getMethod(): string
    {
        return strtoupper($this->server['REQUEST_METHOD'] ?? 'GET');
    }

    /**
     * Retorna a URI completa da requisição
     * 
     * @return string URI da requisição (ex: /users?id=1)
     */
    public function getUri(): string
    {
        return $this->server['REQUEST_URI'] ?? '/';
    }

    /**
     * Retorna o caminho da URI sem a query string
     * 
     * @return string Caminho da URI (ex: /users)
     */
    public function getPath(): string
    {
        $uri = $this->getUri();
        $position = strpos($uri, '?');
        
        if ($position !== false) {
            return substr($uri, 0, $position);
        }
        
        return $uri;
    }

    /**
     * Obtém um parâmetro GET específico ou todos os parâmetros
     * 
     * @param string|null $key Chave do parâmetro (null para retornar todos)
     * @param mixed $default Valor padrão se a chave não existir
     * @return mixed Valor do parâmetro ou array com todos os parâmetros
     */
    public function getQuery(string $key = null, $default = null)
    {
        if ($key === null) {
            return $this->get;
        }

        return $this->get[$key] ?? $default;
    }

    /**
     * Obtém um parâmetro POST específico ou todos os parâmetros
     * 
     * @param string|null $key Chave do parâmetro (null para retornar todos)
     * @param mixed $default Valor padrão se a chave não existir
     * @return mixed Valor do parâmetro ou array com todos os parâmetros
     */
    public function getPost(string $key = null, $default = null)
    {
        if ($key === null) {
            return $this->post;
        }

        return $this->post[$key] ?? $default;
    }

    /**
     * Extrai os headers HTTP das variáveis do servidor
     * 
     * Converte as chaves HTTP_* do $_SERVER em headers
     * formatados corretamente (ex: HTTP_ACCEPT_LANGUAGE -> Accept-Language)
     * 
     * @return array Headers HTTP formatados
     */
    protected function getHeaders(): array
    {
        $headers = [];
        foreach ($this->server as $key => $value) {
            if (strpos($key, 'HTTP_') === 0) {
                $header = str_replace(' ', '-', ucwords(str_replace('_', ' ', strtolower(substr($key, 5)))));
                $headers[$header] = $value;
            }
        }
        return $headers;
    }

    /**
     * Obtém um header HTTP específico
     * 
     * @param string $key Nome do header
     * @return string|null Valor do header ou null se não existir
     */
    public function getHeader(string $key): ?string
    {
        return $this->headers[$key] ?? null;
    }

    /**
     * Verifica se o método da requisição corresponde ao método especificado
     * 
     * @param string $method Método HTTP a ser verificado
     * @return bool True se o método corresponder
     */
    public function isMethod(string $method): bool
    {
        return $this->getMethod() === strtoupper($method);
    }

    /**
     * Retorna todos os dados da requisição (GET, POST, etc)
     * Compatível com Laravel
     * 
     * @return array
     */
    public function all(): array
    {
        return array_merge($this->get, $this->post);
    }

    /**
     * Retorna os dados do POST
     * Compatível com Laravel
     * 
     * @return array
     */
    public function post(): array
    {
        return $this->post;
    }

    /**
     * Retorna os dados do GET
     * Compatível com Laravel
     * 
     * @return array
     */
    public function get(): array
    {
        return $this->get;
    }

    /**
     * Retorna um valor específico da requisição
     * Compatível com Laravel
     * 
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function input(string $key, $default = null)
    {
        return $this->all()[$key] ?? $default;
    }

    /**
     * Retorna apenas os valores das chaves especificadas
     * Compatível com Laravel
     * 
     * @param array $keys
     * @return array
     */
    public function only(array $keys): array
    {
        $data = $this->all();
        return array_intersect_key($data, array_flip($keys));
    }

    /**
     * Retorna todos os valores exceto as chaves especificadas
     * Compatível com Laravel
     * 
     * @param array $keys
     * @return array
     */
    public function except(array $keys): array
    {
        $data = $this->all();
        return array_diff_key($data, array_flip($keys));
    }

    /**
     * Verifica se uma chave existe na requisição
     * Compatível com Laravel
     * 
     * @param string $key
     * @return bool
     */
    public function has(string $key): bool
    {
        return isset($this->all()[$key]);
    }

    /**
     * Retorna os headers da requisição
     * Compatível com Laravel
     * 
     * @return array
     */
    public function headers(): array
    {
        return $this->headers;
    }

    /**
     * Retorna um header específico
     * Compatível com Laravel
     * 
     * @param string $key
     * @return string|null
     */
    public function header(string $key): ?string
    {
        return $this->getHeader($key);
    }

    /**
     * Retorna o método HTTP da requisição
     * Compatível com Laravel
     * 
     * @return string
     */
    public function method(): string
    {
        return $this->getMethod();
    }

    /**
     * Verifica se a requisição é do tipo JSON
     * Compatível com Laravel
     * 
     * @return bool
     */
    public function isJson(): bool
    {
        return str_contains($this->getHeader('Content-Type') ?? '', 'application/json');
    }

    /**
     * Retorna os dados JSON da requisição
     * Compatível com Laravel
     * 
     * @return array
     */
    public function json(): array
    {
        if ($this->isJson()) {
            $content = file_get_contents('php://input');
            return json_decode($content, true) ?? [];
        }
        return [];
    }

    /**
     * Dump and Die - Exibe os dados e encerra a execução
     * Compatível com Laravel
     * 
     * @param mixed ...$vars Variáveis para exibir
     * @return never
     */
    public function dd(...$vars): never
    {
        $this->dump(...$vars);
        exit(1);
    }

    /**
     * Dump - Exibe os dados sem encerrar a execução
     * Compatível com Laravel
     * 
     * @param mixed ...$vars Variáveis para exibir
     * @return void
     */
    public function dump(...$vars): void
    {
        echo '<pre style="background: #f5f5f5; padding: 15px; border-radius: 5px; border: 1px solid #ddd; margin: 10px;">';
        foreach ($vars as $var) {
            var_dump($var);
        }
        echo '</pre>';
    }

    /**
     * Exibe todos os dados da requisição de forma organizada
     * 
     * @return array
     */
    public function toArray(): array
    {
        return [
            'method' => $this->method(),
            'uri' => $this->getUri(),
            'path' => $this->getPath(),
            'headers' => $this->headers(),
            'content_type' => $this->header('Content-Type'),
            'is_json' => $this->isJson(),
            'get' => $this->get(),
            'post' => $this->post(),
            'json' => $this->json(),
            'files' => $this->files,
            'cookies' => $this->cookies,
            'server' => [
                'REQUEST_METHOD' => $this->server['REQUEST_METHOD'] ?? null,
                'REQUEST_URI' => $this->server['REQUEST_URI'] ?? null,
                'HTTP_HOST' => $this->server['HTTP_HOST'] ?? null,
                'REMOTE_ADDR' => $this->server['REMOTE_ADDR'] ?? null,
                'HTTP_USER_AGENT' => $this->server['HTTP_USER_AGENT'] ?? null,
            ]
        ];
    }
} 