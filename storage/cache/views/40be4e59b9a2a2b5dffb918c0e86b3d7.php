<?php $__sections = array (
  'content' => '<div class="header">
        <h1>Bem-vindo ao LadyPHP</h1>
        <p>Um framework PHP simples e elegante</p>
    </div>

    <div class="content">
                    <h2>Bem-vindo ao seu novo projeto!</h2>
        
        <h3>Recursos Disponíveis:</h3>
        <ul>
            array(5) {
  [0]=>
  string(24) "Sistema de Rotas Simples"
  [1]=>
  string(20) "Validação de Dados"
  [2]=>
  string(23) "Sistema de Views (Elle)"
  [3]=>
  string(18) "Controladores HTTP"
  [4]=>
  string(14) "Respostas JSON"
}
<!-- Debug: Iniciando foreach -->                            <!-- Debug: Iterando feature: Sistema de Rotas Simples -->                <li>Sistema de Rotas Simples</li>
                            <!-- Debug: Iterando feature: Validação de Dados -->                <li>Validação de Dados</li>
                            <!-- Debug: Iterando feature: Sistema de Views (Elle) -->                <li>Sistema de Views (Elle)</li>
                            <!-- Debug: Iterando feature: Controladores HTTP -->                <li>Controladores HTTP</li>
                            <!-- Debug: Iterando feature: Respostas JSON -->                <li>Respostas JSON</li>
                        <!-- Debug: Fim do foreach -->        </ul>

                    <div style="margin-top: 20px; padding: 15px; background: #e9ecef; border-radius: 4px;">
                <h4>Informações Adicionais:</h4>
                <p>Este é um framework PHP minimalista e elegante, inspirado em Laravel mas com uma abordagem mais simples.</p>
            </div>
            </div>',
  'styles' => '<style>
    .header {
        text-align: center;
        margin-bottom: 30px;
    }
    .content {
        background: #f8f9fa;
        padding: 20px;
        border-radius: 5px;
        border: 1px solid #ddd;
    }
    .content ul {
        list-style: none;
        padding: 0;
    }
    .content li {
        padding: 8px 0;
        border-bottom: 1px solid #eee;
    }
    .content li:last-child {
        border-bottom: none;
    }
</style>',
  'title' => 'Bem-vindo ao LadyPHP',
); ?><!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($__sections["title"]) ? $__sections["title"] : "LadyPHP"; ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            line-height: 1.6;
            color: #333;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }
        header {
            background: #f8f9fa;
            padding: 1rem 0;
            border-bottom: 1px solid #ddd;
        }
        nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        nav a {
            color: #333;
            text-decoration: none;
            padding: 0.5rem 1rem;
        }
        nav a:hover {
            color: #007bff;
        }
        main {
            padding: 2rem 0;
        }
        footer {
            background: #f8f9fa;
            padding: 1rem 0;
            text-align: center;
            border-top: 1px solid #ddd;
            margin-top: 2rem;
        }
    </style>
    <?php echo isset($__sections["styles"]) ? $__sections["styles"] : ""; ?>
</head>
<body>
    <header>
        <div class="container">
            <nav>
                <a href="/">LadyPHP</a>
                <div>
                    <a href="/">Home</a>
                    <a href="/about">Sobre</a>
                    <a href="/contact">Contato</a>
                </div>
            </nav>
        </div>
    </header>

    <main>
        <div class="container">
            <?php echo isset($__sections["content"]) ? $__sections["content"] : ""; ?>
        </div>
    </main>

    <footer>
        <div class="container">
            <p>&copy; <?php echo htmlspecialchars(isset($year) ? $year : date('Y')); ?> LadyPHP. Todos os direitos reservados.</p>
        </div>
    </footer>

    <?php echo isset($__sections["scripts"]) ? $__sections["scripts"] : ""; ?>
</body>
</html> 