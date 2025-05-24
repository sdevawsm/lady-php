<?php

namespace Database\Migrations;

use LadyPHP\Database\Migration;

class CreateUsersTable extends Migration
{
    public function up(): void
    {
        $this->createTable('table_name', function($table) {
            $table->id();
            // Adicione suas colunas aqui
            $table->timestamps();
        });
    }

    public function down(): void
    {
        $this->dropTable('table_name');
    }
}