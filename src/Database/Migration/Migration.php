<?php

namespace LadyPHP\Database\Migration;

use PDO;
use LadyPHP\Database\Migration\Schema;
use LadyPHP\Database\Migration\Blueprint;

abstract class Migration
{
    protected PDO $connection;
    protected Schema $schema;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
        $this->schema = new Schema($connection);
    }

    /**
     * Executa a migração
     */
    abstract public function up(): void;

    /**
     * Reverte a migração
     */
    abstract public function down(): void;

    /**
     * Cria uma nova tabela
     */
    protected function createTable(string $table, callable $callback): void
    {
        $this->table = $table;
        $blueprint = new Blueprint($table);
        $callback($blueprint);
        
        $sql = $blueprint->toSql();
        $this->connection->exec($sql);
    }

    /**
     * Remove uma tabela
     */
    protected function dropTable(string $table): void
    {
        $this->connection->exec("DROP TABLE IF EXISTS {$table}");
    }

    /**
     * Verifica se uma tabela existe
     */
    protected function hasTable(string $table): bool
    {
        $sql = "SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = ?";
        $stmt = $this->connection->prepare($sql);
        $stmt->execute([$table]);
        return (bool) $stmt->fetchColumn();
    }

    /**
     * Verifica se uma coluna existe em uma tabela
     */
    protected function hasColumn(string $table, string $column): bool
    {
        $sql = "SELECT COUNT(*) FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = ? AND column_name = ?";
        $stmt = $this->connection->prepare($sql);
        $stmt->execute([$table, $column]);
        return (bool) $stmt->fetchColumn();
    }
} 