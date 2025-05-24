<?php

namespace LadyPHP\View;

use LadyPHP\Http\Response;

class View
{
    private static ?ElleCompiler $compiler = null;
    private static string $viewPath;
    private static string $cachePath;

    public static function init(string $viewPath, string $cachePath): void
    {
        self::$viewPath = $viewPath;
        self::$cachePath = $cachePath;
        self::$compiler = new ElleCompiler($viewPath, $cachePath);
    }

    public static function render(string $view, array $data = []): Response
    {
        if (self::$compiler === null) {
            throw new \Exception('View não foi inicializada. Chame View::init() primeiro.');
        }

        $compiledFile = self::$compiler->compile($view, $data);
        
        ob_start();
        include $compiledFile;
        $content = ob_get_clean();

        return new Response($content);
    }
} 