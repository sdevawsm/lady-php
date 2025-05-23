<?php

/**
 * Arquivo server.php
 * 
 * Este arquivo serve como ponto de entrada para o servidor PHP.
 * Ele verifica se a requisição é para um arquivo estático no diretório public
 * e, caso contrário, redireciona para o index.php.
 * 
 * Uso:
 * php -S localhost:8080 server.php
 */

$uri = urldecode(
    parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? ''
);

// This file allows us to emulate Apache's "mod_rewrite" functionality from the
// built-in PHP web server. This provides a convenient way to test a Laravel
// application without having installed a "real" web server software here.
if ($uri !== '/' && file_exists(__DIR__.'/public'.$uri)) {
    return false;
}

require_once __DIR__.'/public/index.php'; 