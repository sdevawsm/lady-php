<?php

namespace App\Models;

use App\Models\Base\Model;
use App\Models\Traits\HasTimestamps;

class Category extends Model
{
    use HasTimestamps;

    protected static string $table = 'categories';
    
    protected static array $fillable = [
        'name',
        'slug',
        'description',
        'active',
        'created_at',
        'updated_at'
    ];

    /**
     * Retorna todos os produtos desta categoria
     */
    public function products(): array
    {
        return Product::query()->where('category_id', '=', (int)$this->id)->get();
    }
} 