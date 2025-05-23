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
} 