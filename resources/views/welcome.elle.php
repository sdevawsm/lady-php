<!DOCTYPE html>
<html>
<head>
    <title>Bem-vindo ao LadyPHP</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            line-height: 1.6;
        }
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
        .footer {
            margin-top: 30px;
            text-align: center;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Bem-vindo ao LadyPHP</h1>
        <p>Um framework PHP simples e elegante</p>
    </div>

    <div class="content">
        @if($message)
            <h2>{{ $message }}</h2>
        @endif

        <h3>Recursos Disponíveis:</h3>
        <ul>
            @foreach($features as $feature)
                <li>{{ $feature }}</li>
            @endforeach
        </ul>

        @if($showExtra)
            <div style="margin-top: 20px; padding: 15px; background: #e9ecef; border-radius: 4px;">
                <h4>Informações Adicionais:</h4>
                <p>{{ $extraInfo }}</p>
            </div>
        @endif
    </div>

    <div class="footer">
        <p>LadyPHP &copy; {{ $year }}</p>
    </div>
</body>
</html> 