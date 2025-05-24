<?php

namespace Database\Migrations;

use LadyPHP\Database\Migration;
use LadyPHP\Database\Blueprint;

class CreateProductsTable extends Migration
{
    public function up(): void
    {
        $this->schema->create('products', function(Blueprint $table) {
            $table->id();
            $table->string('name', 100)->notNull();
            $table->string('slug', 100)->notNull()->unique();
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2)->notNull();
            $table->integer('stock')->default(0);
            $table->boolean('active')->default(true);
            
            // Chave estrangeira para categories
            $table->bigInteger('category_id')->unsigned();
            $table->foreign('category_id')
                  ->references('id')
                  ->on('categories')
                  ->onDelete('CASCADE');
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        $this->schema->drop('products');
    }
} 