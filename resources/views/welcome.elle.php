@extends('layouts/app')

@section('title', 'Bem-vindo ao LadyPHP')

@section('content')
    {{-- Cabeçalho da página --}}
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
            <?php 
            //var_dump($features);  // Debug para ver o conteúdo de $features
            echo "<!-- Debug: Iniciando foreach -->"; 
            ?>
            
            {{-- 
                Lista de itens
                Esta seção mostra os itens disponíveis
            --}}
            @foreach($features as $feature)
                <?php echo "<!-- Debug: Iterando feature: " . htmlspecialchars($feature) . " -->"; ?>
                <li>{{ $feature }}</li>
            @endforeach
            <?php echo "<!-- Debug: Fim do foreach -->"; ?>
        </ul>

        @if($showExtra)
            <div style="margin-top: 20px; padding: 15px; background: #e9ecef; border-radius: 4px;">
                <h4>Informações Adicionais:</h4>
                <p>{{ $extraInfo }}</p>
            </div>
        @endif


    </div>
@endsection

@section('styles')
<style>
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
    .status-box {
        margin-top: 20px;
        padding: 15px;
        background: #fff;
        border: 1px solid #ddd;
        border-radius: 4px;
    }
    .status-active {
        color: #28a745;
        font-weight: bold;
    }
    .status-pending {
        color: #ffc107;
        font-weight: bold;
    }
    .status-unknown {
        color: #dc3545;
        font-weight: bold;
    }
</style>
@endsection 