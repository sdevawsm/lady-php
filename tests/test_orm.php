<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Models\Category;
use App\Models\Product;
use App\Models\Base\Model;
use LadyPHP\Database\Config\Config;

// Carregar configurações do .env
Config::load();

// Configurar conexão PDO usando as configurações do .env
$pdo = new PDO(
    Config::getDsn(),
    Config::getCredentials()['username'],
    Config::getCredentials()['password'],
    Config::getPdoOptions()
);

Model::setConnection($pdo);

try {
    // Criar categorias
    echo "Criando categorias...\n";
    
    $eletronicos = Category::create([
        'name' => 'Eletrônicos',
        'slug' => 'eletronicos',
        'description' => 'Produtos eletrônicos em geral'
    ]);
    echo "Categoria criada: {$eletronicos->name}\n";

    $informatica = Category::create([
        'name' => 'Informática',
        'slug' => 'informatica',
        'description' => 'Produtos de informática'
    ]);
    echo "Categoria criada: {$informatica->name}\n";

    $celulares = Category::create([
        'name' => 'Celulares',
        'slug' => 'celulares',
        'description' => 'Smartphones e acessórios'
    ]);
    echo "Categoria criada: {$celulares->name}\n";

    // Criar produtos
    echo "\nCriando produtos...\n";

    $smartphone = Product::create([
        'name' => 'Smartphone XYZ',
        'slug' => 'smartphone-xyz',
        'description' => 'Um smartphone incrível com câmera de 48MP',
        'price' => 1999.99,
        'stock' => 10,
        'category_id' => $celulares->id
    ]);
    echo "Produto criado: {$smartphone->name} - R$ {$smartphone->price}\n";

    $notebook = Product::create([
        'name' => 'Notebook ABC',
        'slug' => 'notebook-abc',
        'description' => 'Notebook potente com 16GB de RAM',
        'price' => 4999.99,
        'stock' => 5,
        'category_id' => $informatica->id
    ]);
    echo "Produto criado: {$notebook->name} - R$ {$notebook->price}\n";

    $tablet = Product::create([
        'name' => 'Tablet 123',
        'slug' => 'tablet-123',
        'description' => 'Tablet fino e leve com tela de 10 polegadas',
        'price' => 1499.99,
        'stock' => 8,
        'category_id' => $eletronicos->id
    ]);
    echo "Produto criado: {$tablet->name} - R$ {$tablet->price}\n";

    // Testar consultas
    echo "\nTestando consultas...\n";

    // Buscar todos os produtos
    echo "\nTodos os produtos:\n";
    $produtos = Product::all();
    foreach ($produtos as $produto) {
        echo "- {$produto->name} (R$ {$produto->price})\n";
    }

    // Buscar produtos com preço maior que 2000
    echo "\nProdutos acima de R$ 2000:\n";
    $produtos = Product::where('price', '>', 2000)->get();
    foreach ($produtos as $produto) {
        echo "- {$produto->name} (R$ {$produto->price})\n";
    }

    // Buscar produtos de uma categoria específica
    echo "\nProdutos da categoria Celulares:\n";
    var_dump($celulares->id); // Debug para ver o valor do ID
    var_dump($celulares->toArray()); // Debug para ver todos os atributos
    $produtos = Product::where('category_id', '=', (int)$celulares->id)->get(); // Forçar conversão para inteiro
    foreach ($produtos as $produto) {
        echo "- {$produto->name}\n";
    }

    // Buscar produtos com estoque
    echo "\nProdutos com estoque:\n";
    $produtos = Product::where('stock', '>', 0)->get();
    foreach ($produtos as $produto) {
        echo "- {$produto->name} (Estoque: {$produto->stock})\n";
    }

    // Buscar produtos ordenados por preço
    echo "\nProdutos ordenados por preço (mais caros primeiro):\n";
    $produtos = Product::query()->orderBy('price', 'desc')->get();
    foreach ($produtos as $produto) {
        echo "- {$produto->name} (R$ {$produto->price})\n";
    }

    // Testar relacionamentos
    echo "\nTestando relacionamentos:\n";
    
    // Buscar categoria de um produto
    $produto = Product::first();
    $categoria = $produto->category();
    echo "Categoria do produto {$produto->name}: {$categoria->name}\n";

    // Buscar produtos de uma categoria
    $categoria = Category::first();
    $produtos = $categoria->products();
    echo "\nProdutos da categoria {$categoria->name}:\n";
    foreach ($produtos as $produto) {
        echo "- {$produto->name}\n";
    }

} catch (\Exception $e) {
    echo "Erro: " . $e->getMessage() . "\n";
} 