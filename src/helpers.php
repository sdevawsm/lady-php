<?php

use LadyPHP\Routing\RouteFacade as Route;

if (!function_exists('route')) {
    /**
     * Retorna uma instância da fachada de rotas
     *
     * @return \LadyPHP\Routing\RouteFacade
     */
    function route(): \LadyPHP\Routing\RouteFacade
    {
        return Route::class;
    }
}

if (!function_exists('dd')) {
    /**
     * Dump and Die - Exibe os dados e encerra a execução
     * Compatível com Laravel
     * 
     * @param mixed ...$vars Variáveis para exibir
     * @return never
     */
    function dd(...$vars): never
    {
        echo '<pre style="background: #f5f5f5; padding: 15px; border-radius: 5px; border: 1px solid #ddd; margin: 10px;">';
        foreach ($vars as $var) {
            var_dump($var);
        }
        echo '</pre>';
        exit(1);
    }
}

if (!function_exists('dump')) {
    /**
     * Dump - Exibe os dados sem encerrar a execução
     * Compatível com Laravel
     * 
     * @param mixed ...$vars Variáveis para exibir
     * @return void
     */
    function dump(...$vars): void
    {
        echo '<pre style="background: #f5f5f5; padding: 15px; border-radius: 5px; border: 1px solid #ddd; margin: 10px;">';
        foreach ($vars as $var) {
            var_dump($var);
        }
        echo '</pre>';
    }
} 