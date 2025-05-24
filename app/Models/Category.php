<?php

namespace App\Models;

use LadyPHP\Database\Model;

class Category extends Model
{
    protected static string $table = 'categories';
    
    protected static array $fillable = [
        'name',
        'slug',
        'description',
        'active'
    ];

    /**
     * Retorna todos os produtos desta categoria
     */
    public function products(): array
    {
        return Product::query()->where('category_id', '=', (int)$this->id)->get();
    }
} 