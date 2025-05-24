<?php

namespace Database\Migrations;

use LadyPHP\Database\Migration;
use LadyPHP\Database\Blueprint;

class CreateCategoriesTable extends Migration
{
    public function up(): void
    {
        $this->schema->create('categories', function(Blueprint $table) {
            $table->id();
            $table->string('name', 100)->notNull();
            $table->string('slug', 100)->notNull()->unique();
            $table->text('description')->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        $this->schema->drop('categories');
    }
} 