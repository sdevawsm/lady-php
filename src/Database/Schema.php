<?php

namespace LadyPHP\Database;

use PDO;

class Schema
{
    private PDO $connection;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    /**
     * Cria uma nova tabela
     */
    public function create(string $table, callable $callback): void
    {
        $blueprint = new Blueprint($table);
        $callback($blueprint);
        
        $sql = $blueprint->toSql();
        $this->connection->exec($sql);
    }

    /**
     * Remove uma tabela
     */
    public function drop(string $table): void
    {
        $this->connection->exec("DROP TABLE IF EXISTS `{$table}`");
    }

    /**
     * Verifica se uma tabela existe
     */
    public function hasTable(string $table): bool
    {
        $sql = "SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = ?";
        $stmt = $this->connection->prepare($sql);
        $stmt->execute([$table]);
        return (bool) $stmt->fetchColumn();
    }

    /**
     * Verifica se uma coluna existe em uma tabela
     */
    public function hasColumn(string $table, string $column): bool
    {
        $sql = "SELECT COUNT(*) FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = ? AND column_name = ?";
        $stmt = $this->connection->prepare($sql);
        $stmt->execute([$table, $column]);
        return (bool) $stmt->fetchColumn();
    }
} 