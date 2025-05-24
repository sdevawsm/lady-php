<?php

namespace LadyPHP\Database;

class Blueprint
{
    private string $table;
    private array $columns = [];
    private array $indexes = [];
    private array $foreignKeys = [];
    private ?string $primaryKey = null;

    public function __construct(string $table)
    {
        $this->table = $table;
    }

    /**
     * Adiciona uma coluna id auto incremento
     */
    public function id(): self
    {
        return $this->bigIncrements('id');
    }

    /**
     * Adiciona uma coluna bigint auto incremento
     */
    public function bigIncrements(string $column): self
    {
        $this->columns[] = "`{$column}` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY";
        $this->primaryKey = $column;
        return $this;
    }

    /**
     * Adiciona uma coluna string (VARCHAR)
     */
    public function string(string $column, int $length = 255): self
    {
        $this->columns[] = "`{$column}` VARCHAR({$length})";
        return $this;
    }

    /**
     * Adiciona uma coluna text
     */
    public function text(string $column): self
    {
        $this->columns[] = "`{$column}` TEXT";
        return $this;
    }

    /**
     * Adiciona uma coluna integer
     */
    public function integer(string $column): self
    {
        $this->columns[] = "`{$column}` INT";
        return $this;
    }

    /**
     * Adiciona uma coluna bigint
     */
    public function bigInteger(string $column): self
    {
        $this->columns[] = "`{$column}` BIGINT";
        return $this;
    }

    /**
     * Adiciona uma coluna boolean
     */
    public function boolean(string $column): self
    {
        $this->columns[] = "`{$column}` BOOLEAN";
        return $this;
    }

    /**
     * Adiciona uma coluna timestamp
     */
    public function timestamp(string $column): self
    {
        $this->columns[] = "`{$column}` TIMESTAMP";
        return $this;
    }

    /**
     * Adiciona timestamps created_at e updated_at
     */
    public function timestamps(): self
    {
        $this->timestamp('created_at')->nullable();
        $this->timestamp('updated_at')->nullable();
        return $this;
    }

    /**
     * Define uma coluna como nullable
     */
    public function nullable(): self
    {
        $lastColumn = array_pop($this->columns);
        $this->columns[] = $lastColumn . " NULL";
        return $this;
    }

    /**
     * Define uma coluna como not null
     */
    public function notNull(): self
    {
        $lastColumn = array_pop($this->columns);
        $this->columns[] = $lastColumn . " NOT NULL";
        return $this;
    }

    /**
     * Define um valor padrão para a coluna
     */
    public function default($value): self
    {
        $lastColumn = array_pop($this->columns);
        $default = is_string($value) ? "'{$value}'" : $value;
        $this->columns[] = $lastColumn . " DEFAULT {$default}";
        return $this;
    }

    /**
     * Adiciona um índice
     */
    public function index(string|array $columns, ?string $name = null): self
    {
        $columns = is_array($columns) ? $columns : [$columns];
        $name = $name ?? "idx_{$this->table}_" . implode('_', $columns);
        $columns = array_map(fn($col) => "`{$col}`", $columns);
        $this->indexes[] = "INDEX `{$name}` (" . implode(', ', $columns) . ")";
        return $this;
    }

    /**
     * Adiciona uma chave estrangeira
     */
    public function foreign(string $column): ForeignKeyDefinition
    {
        return new ForeignKeyDefinition($this, $column);
    }

    /**
     * Gera o SQL para criar a tabela
     */
    public function toSql(): string
    {
        $sql = "CREATE TABLE IF NOT EXISTS `{$this->table}` (\n";
        $sql .= implode(",\n", array_merge($this->columns, $this->indexes, $this->foreignKeys));
        $sql .= "\n) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
        return $sql;
    }

    /**
     * Adiciona uma definição de chave estrangeira
     */
    public function addForeignKey(string $sql): void
    {
        $this->foreignKeys[] = $sql;
    }
} 