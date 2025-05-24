<?php

namespace LadyPHP\View;

class ElleCompiler
{
    private string $viewPath;
    private string $cachePath;
    private array $data = [];
    private array $sections = [];
    private ?string $currentLayout = null;

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
        $this->sections = []; // Limpa as seções a cada nova compilação
        $this->currentLayout = null; // Limpa o layout atual
        
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
            // Primeiro, processa a view para coletar as seções
            $viewContent = file_get_contents($viewFile);
            
            // Processa o conteúdo da view para converter as diretivas em PHP
            $viewContent = $this->parseDirectives($viewContent);
            
            // Processa as seções e o layout
            $this->processSections($viewContent);

            // Se houver um layout, processa-o com as seções coletadas
            if ($this->currentLayout !== null) {
                $layoutFile = $this->viewPath . '/' . $this->currentLayout . '.elle.php';
                if (!file_exists($layoutFile)) {
                    throw new \Exception("Layout {$this->currentLayout} não encontrado");
                }
                $layoutContent = file_get_contents($layoutFile);
                $compiled = $this->parseLayout($layoutContent);
            } else {
                $compiled = $viewContent;
            }

            if (file_put_contents($cacheFile, $compiled) === false) {
                throw new \Exception("Não foi possível escrever no arquivo de cache: {$cacheFile}");
            }
        }

        return $cacheFile;
    }

    private function parseDirectives(string $content): string
    {
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

        return preg_replace(array_keys($patterns), array_values($patterns), $content);
    }

    private function processSections(string $content): void
    {
        // Processa @extends
        if (preg_match('/@extends\s*\(\s*[\'"]([^\'"]+)[\'"]\s*\)/', $content, $matches)) {
            $this->currentLayout = $matches[1];
        }

        // Processa @section
        preg_match_all('/@section\s*\(\s*[\'"]([^\'"]+)[\'"]\s*(?:,\s*[\'"]([^\'"]*)[\'"])?\s*\)(.*?)@endsection/s', $content, $matches, PREG_SET_ORDER);
        
        foreach ($matches as $match) {
            $name = $match[1];
            $content = isset($match[3]) ? $match[3] : $match[2];
            $this->sections[$name] = $this->parseDirectives($content);
        }
    }

    private function parseLayout(string $content): string
    {
        // Converte as diretivas do Elle para PHP, mas apenas as necessárias para o layout
        $patterns = [
            // {{ $variavel }}
            '/\{\{\s*\$([a-zA-Z0-9_]+)\s*\}\}/' => '<?php echo htmlspecialchars($this->data["$1"] ?? ""); ?>',
            
            // @yield('nome', 'valor padrão')
            '/@yield\s*\(\s*[\'"]([^\'"]+)[\'"]\s*(?:,\s*[\'"]([^\'"]*)[\'"])?\s*\)/' => function($matches) {
                $name = $matches[1];
                $default = $matches[2] ?? '';
                return '<?php echo isset($this->sections["' . $name . '"]) ? $this->sections["' . $name . '"] : "' . $default . '"; ?>';
            }
        ];

        $content = preg_replace_callback(array_keys($patterns), function($matches) use ($patterns) {
            $pattern = $matches[0];
            $replacement = $patterns[$pattern];
            
            if (is_callable($replacement)) {
                return $replacement($matches);
            }
            
            return $replacement;
        }, $content);
        
        return $content;
    }
} 