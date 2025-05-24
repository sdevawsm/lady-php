<?php

namespace LadyPHP\Database;

class ForeignKeyDefinition
{
    private Blueprint $blueprint;
    private string $column;
    private ?string $references = null;
    private ?string $on = null;
    private ?string $onDelete = null;
    private ?string $onUpdate = null;

    public function __construct(Blueprint $blueprint, string $column)
    {
        $this->blueprint = $blueprint;
        $this->column = $column;
    }

    /**
     * Define a tabela e coluna referenciada
     */
    public function references(string $column): self
    {
        $this->references = $column;
        return $this;
    }

    /**
     * Define a tabela referenciada
     */
    public function on(string $table): self
    {
        $this->on = $table;
        return $this;
    }

    /**
     * Define a ação ON DELETE
     */
    public function onDelete(string $action): self
    {
        $this->onDelete = strtoupper($action);
        return $this;
    }

    /**
     * Define a ação ON UPDATE
     */
    public function onUpdate(string $action): self
    {
        $this->onUpdate = strtoupper($action);
        return $this;
    }

    /**
     * Gera o SQL para a chave estrangeira
     */
    public function toSql(): string
    {
        if (!$this->references || !$this->on) {
            throw new \Exception('Foreign key definition is incomplete. Both references() and on() must be called.');
        }

        $sql = "CONSTRAINT `fk_{$this->blueprint->getTable()}_{$this->column}` ";
        $sql .= "FOREIGN KEY (`{$this->column}`) ";
        $sql .= "REFERENCES `{$this->on}` (`{$this->references}`)";

        if ($this->onDelete) {
            $sql .= " ON DELETE {$this->onDelete}";
        }

        if ($this->onUpdate) {
            $sql .= " ON UPDATE {$this->onUpdate}";
        }

        return $sql;
    }
} 