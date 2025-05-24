<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'LadyPHP')</title>
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
    @yield('styles')
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
            @yield('content')
        </div>
    </main>

    <footer>
        <div class="container">
            <p>&copy; {{ $year ?? date('Y') }} LadyPHP. Todos os direitos reservados.</p>
        </div>
    </footer>

    @yield('scripts')
</body>
</html> 