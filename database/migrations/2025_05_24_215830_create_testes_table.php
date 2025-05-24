<?php

namespace Database\Migrations;

use LadyPHP\Database\Migration;
use LadyPHP\Database\Blueprint;

class CreateTestesTable extends Migration
{
    public function up(): void
    {
        $this->schema->create('testes', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        $this->schema->drop('testes');
    }
}