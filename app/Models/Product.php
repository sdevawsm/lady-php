<?php

namespace App\Models;

use App\Models\Base\Model;
use App\Models\Traits\HasTimestamps;

class Product extends Model
{
    use HasTimestamps;

    protected static string $table = 'products';
    
    protected static array $fillable = [
        'name',
        'slug',
        'description',
        'price',
        'stock',
        'active',
        'category_id',
        'created_at',
        'updated_at'
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