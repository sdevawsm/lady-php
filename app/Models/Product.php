<?php

namespace App\Models;

use LadyPHP\Database\Model;

class Product extends Model
{
    protected static string $table = 'products';
    
    protected static array $fillable = [
        'name',
        'slug',
        'description',
        'price',
        'stock',
        'active',
        'category_id'
    ];

    /**
     * Retorna a categoria deste produto
     */
    public function category(): ?Category
    {
        return Category::query()->where('id', '=', (int)$this->category_id)->first();
    }

    /**
     * Define a categoria do produto
     */
    public function setCategory(Category $category): void
    {
        $this->category_id = $category->id;
    }
} 