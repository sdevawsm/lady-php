<?php

namespace LadyPHP\Http;

/**
 * Classe Response
 * 
 * Representa uma resposta HTTP e fornece métodos para
 * construir e enviar respostas de forma organizada.
 * 
 * Esta classe encapsula a lógica de envio de respostas HTTP,
 * incluindo headers, status code e conteúdo.
 */
class Response
{
    /**
     * Conteúdo da resposta
     * Pode ser string, array, objeto, etc
     */
    protected $content;

    /**
     * Código de status HTTP
     * Ex: 200, 404, 500, etc
     */
    protected int $statusCode;

    /**
     * Headers HTTP da resposta
     * Array associativo de headers
     */
    protected array $headers;

    /**
     * Construtor da classe
     * 
     * @param mixed $content Conteúdo da resposta
     * @param int $statusCode Código de status HTTP
     * @param array $headers Headers HTTP adicionais
     */
    public function __construct($content = '', int $statusCode = 200, array $headers = [])
    {
        $this->content = $content;
        $this->statusCode = $statusCode;
        $this->headers = array_merge([
            'Content-Type' => 'text/html; charset=UTF-8'
        ], $headers);
    }

    /**
     * Define o conteúdo da resposta
     * 
     * @param mixed $content Novo conteúdo
     * @return self
     */
    public function setContent($content): self
    {
        $this->content = $content;
        return $this;
    }

    /**
     * Define o código de status HTTP
     * 
     * @param int $statusCode Novo código de status
     * @return self
     */
    public function setStatusCode(int $statusCode): self
    {
        $this->statusCode = $statusCode;
        return $this;
    }

    /**
     * Define um header HTTP
     * 
     * @param string $name Nome do header
     * @param string $value Valor do header
     * @return self
     */
    public function setHeader(string $name, string $value): self
    {
        $this->headers[$name] = $value;
        return $this;
    }

    /**
     * Envia a resposta para o cliente
     * 
     * Este método:
     * 1. Define o código de status HTTP
     * 2. Envia todos os headers
     * 3. Envia o conteúdo da resposta
     * 
     * @return void
     */
    public function send(): void
    {
        // Envia o status code
        http_response_code($this->statusCode);

        // Envia os headers
        foreach ($this->headers as $name => $value) {
            header("$name: $value");
        }

        // Envia o conteúdo
        echo $this->content;
    }

    /**
     * Cria uma resposta JSON
     * 
     * Método estático para facilitar a criação de respostas JSON.
     * Define automaticamente o header Content-Type como application/json
     * e converte o conteúdo para JSON.
     * 
     * @param mixed $data Dados a serem convertidos para JSON
     * @param int $statusCode Código de status HTTP
     * @return self
     * 
     * @example
     * return Response::json(['message' => 'Success'], 200);
     */
    public static function json($data, int $statusCode = 200): self
    {
        return new static(
            json_encode($data),
            $statusCode,
            ['Content-Type' => 'application/json']
        );
    }
} 