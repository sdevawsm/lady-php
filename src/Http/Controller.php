<?php

namespace LadyPHP\Http;

/**
 * Classe base para todos os controladores
 * 
 * Fornece funcionalidades básicas e métodos úteis
 * para todos os controladores da aplicação.
 */
abstract class Controller
{
    /**
     * Retorna uma resposta JSON
     * 
     * @param mixed $data Dados a serem convertidos para JSON
     * @param int $statusCode Código de status HTTP
     * @return Response
     */
    protected function json($data, int $statusCode = 200): Response
    {
        return Response::json($data, $statusCode);
    }

    /**
     * Retorna uma resposta de texto
     * 
     * @param string $content Conteúdo da resposta
     * @param int $statusCode Código de status HTTP
     * @return Response
     */
    protected function text(string $content, int $statusCode = 200): Response
    {
        return new Response($content, $statusCode);
    }

    /**
     * Retorna uma resposta de erro
     * 
     * @param string $message Mensagem de erro
     * @param int $statusCode Código de status HTTP
     * @return Response
     */
    protected function error(string $message, int $statusCode = 400): Response
    {
        return $this->json(['error' => $message], $statusCode);
    }
} 