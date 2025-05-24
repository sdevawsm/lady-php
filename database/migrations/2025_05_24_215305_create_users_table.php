<?php

namespace Database\Migrations;

use LadyPHP\Database\Migration;
use LadyPHP\Database\Blueprint;

class CreateUsersTable extends Migration
{
    public function up(): void
    {
        $this->schema->create('users', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        $this->schema->drop('users');
    }
}