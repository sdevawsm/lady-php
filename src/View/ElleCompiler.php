<?php

namespace LadyPHP\View;

class ElleCompiler
{
    private string $viewPath;
    private string $cachePath;
    private array $data = [];

    public function __construct(string $viewPath, string $cachePath)
    {
        $this->viewPath = rtrim($viewPath, '/');
        $this->cachePath = rtrim($cachePath, '/');
        
        // Cria o diretório de cache se não existir
        if (!is_dir($this->cachePath)) {
            mkdir($this->cachePath, 0777, true);
        }
    }

    public function compile(string $view, array $data = []): string
    {
        $this->data = $data;
        $viewFile = $this->viewPath . '/' . $view . '.elle.php';
        $cacheFile = $this->cachePath . '/' . md5($view) . '.php';

        if (!file_exists($viewFile)) {
            throw new \Exception("View {$view} não encontrada");
        }

        // Garante que o diretório de cache existe
        if (!is_dir($this->cachePath)) {
            if (!mkdir($this->cachePath, 0777, true)) {
                throw new \Exception("Não foi possível criar o diretório de cache: {$this->cachePath}");
            }
        }

        // Compila apenas se o arquivo de cache não existir ou se a view foi modificada
        if (!file_exists($cacheFile) || filemtime($viewFile) > filemtime($cacheFile)) {
            $content = file_get_contents($viewFile);
            $compiled = $this->parseContent($content);
            if (file_put_contents($cacheFile, $compiled) === false) {
                throw new \Exception("Não foi possível escrever no arquivo de cache: {$cacheFile}");
            }
        }

        return $cacheFile;
    }

    private function parseContent(string $content): string
    {
        // Converte as diretivas do Elle para PHP
        $patterns = [
            // {{ $variavel }}
            '/\{\{\s*\$([a-zA-Z0-9_]+)\s*\}\}/' => '<?php echo htmlspecialchars($this->data["$1"] ?? ""); ?>',
            
            // @if(condição)
            '/@if\s*\((.*?)\)/' => '<?php if($1): ?>',
            
            // @elseif(condição)
            '/@elseif\s*\((.*?)\)/' => '<?php elseif($1): ?>',
            
            // @else
            '/@else/' => '<?php else: ?>',
            
            // @endif
            '/@endif/' => '<?php endif; ?>',
            
            // @foreach($array as $item)
            '/@foreach\s*\(\s*\$([a-zA-Z0-9_]+)\s+as\s+\$([a-zA-Z0-9_]+)\s*\)/' => '<?php foreach($this->data["$1"] ?? [] as $$2): ?>',
            
            // @endforeach
            '/@endforeach/' => '<?php endforeach; ?>',
            
            // @include('view')
            '/@include\s*\(\s*[\'"]([^\'"]+)[\'"]\s*\)/' => '<?php include $this->compile("$1", $this->data); ?>'
        ];

        $content = preg_replace(array_keys($patterns), array_values($patterns), $content);
        
        // Remove comentários do Elle
        $content = preg_replace('/\{\{--.*?--\}\}/s', '', $content);
        
        return $content;
    }
} 