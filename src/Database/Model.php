<?php

namespace LadyPHP\Database;

use PDO;

abstract class Model
{
    protected static PDO $connection;
    protected static string $table;
    protected static string $primaryKey = 'id';
    protected array $attributes = [];
    protected array $original = [];
    protected bool $exists = false;
    protected static array $fillable = [];
    protected static array $guarded = ['id'];
    protected static array $with = [];
    protected static array $withCount = [];

    /**
     * Define a conexão PDO para todos os modelos
     */
    public static function setConnection(PDO $connection): void
    {
        static::$connection = $connection;
    }

    /**
     * Retorna a conexão PDO
     */
    public static function getConnection(): PDO
    {
        return static::$connection;
    }

    /**
     * Retorna o nome da tabela
     */
    public static function getTable(): string
    {
        if (!isset(static::$table)) {
            // Converte o nome da classe para snake_case e pluraliza
            $class = (new \ReflectionClass(static::class))->getShortName();
            static::$table = strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $class)) . 's';
        }
        return static::$table;
    }

    /**
     * Cria uma nova instância do modelo
     */
    public function __construct(array $attributes = [])
    {
        $this->fill($attributes);
    }

    /**
     * Preenche o modelo com atributos
     */
    public function fill(array $attributes): static
    {
        foreach ($attributes as $key => $value) {
            if ($this->isFillable($key)) {
                $this->setAttribute($key, $value);
            }
        }
        return $this;
    }

    /**
     * Verifica se um atributo é preenchível
     */
    protected function isFillable(string $key): bool
    {
        if (in_array($key, static::$guarded)) {
            return false;
        }

        return empty(static::$fillable) || in_array($key, static::$fillable);
    }

    /**
     * Define um atributo
     */
    protected function setAttribute(string $key, $value): void
    {
        $this->attributes[$key] = $value;
    }

    /**
     * Cria um novo modelo e salva no banco
     */
    public static function create(array $attributes): static
    {
        $model = new static($attributes);
        $model->save();
        return $model;
    }

    /**
     * Encontra um registro pelo ID
     */
    public static function find(int $id): ?static
    {
        return static::where(static::$primaryKey, $id)->first();
    }

    /**
     * Encontra um registro pelo ID ou lança uma exceção
     */
    public static function findOrFail(int $id): static
    {
        $model = static::find($id);
        if (!$model) {
            throw new \Exception("Model not found");
        }
        return $model;
    }

    /**
     * Retorna todos os registros
     */
    public static function all(): array
    {
        return static::query()->get();
    }

    /**
     * Cria uma nova query
     */
    public static function query(): QueryBuilder
    {
        return new QueryBuilder(static::class);
    }

    /**
     * Adiciona uma cláusula where
     */
    public static function where(string $column, $operator = null, $value = null): QueryBuilder
    {
        return static::query()->where($column, $operator, $value);
    }

    /**
     * Retorna o primeiro registro
     */
    public static function first(): ?static
    {
        return static::query()->first();
    }

    /**
     * Retorna o primeiro registro ou lança uma exceção
     */
    public static function firstOrFail(): static
    {
        $model = static::first();
        if (!$model) {
            throw new \Exception("Model not found");
        }
        return $model;
    }

    /**
     * Cria um novo modelo a partir de um array de atributos
     */
    public static function newFromBuilder(array $attributes): static
    {
        $model = new static;
        $model->exists = true;
        $model->attributes = $attributes;
        $model->original = $attributes;
        return $model;
    }

    /**
     * Salva o modelo
     */
    public function save(): bool
    {
        if ($this->exists) {
            return $this->update();
        }
        return $this->insert();
    }

    /**
     * Insere um novo registro
     */
    protected function insert(): bool
    {
        $attributes = $this->getDirty();
        
        if (empty($attributes)) {
            return true;
        }

        $columns = implode('`, `', array_keys($attributes));
        $values = implode(', ', array_fill(0, count($attributes), '?'));
        
        $sql = "INSERT INTO " . static::getTable() . " (`{$columns}`) VALUES ({$values})";
        $stmt = static::$connection->prepare($sql);
        
        if ($stmt->execute(array_values($attributes))) {
            $this->exists = true;
            $this->attributes[static::$primaryKey] = static::$connection->lastInsertId();
            $this->original = $this->attributes;
            return true;
        }
        
        return false;
    }

    /**
     * Atualiza um registro existente
     */
    protected function update(): bool
    {
        $dirty = $this->getDirty();
        
        if (empty($dirty)) {
            return true;
        }

        $sets = [];
        foreach (array_keys($dirty) as $column) {
            $sets[] = "`{$column}` = ?";
        }
        
        $sql = "UPDATE " . static::getTable() . " SET " . implode(', ', $sets) . 
               " WHERE " . static::$primaryKey . " = ?";
        
        $values = array_values($dirty);
        $values[] = $this->attributes[static::$primaryKey];
        
        $stmt = static::$connection->prepare($sql);
        
        if ($stmt->execute($values)) {
            $this->original = $this->attributes;
            return true;
        }
        
        return false;
    }

    /**
     * Exclui um registro
     */
    public function delete(): bool
    {
        if (!$this->exists) {
            return false;
        }

        $sql = "DELETE FROM " . static::getTable() . " WHERE " . static::$primaryKey . " = ?";
        $stmt = static::$connection->prepare($sql);
        
        if ($stmt->execute([$this->attributes[static::$primaryKey]])) {
            $this->exists = false;
            $this->attributes = [];
            $this->original = [];
            return true;
        }
        
        return false;
    }

    /**
     * Retorna os atributos que foram modificados
     */
    protected function getDirty(): array
    {
        $dirty = [];
        
        foreach ($this->attributes as $key => $value) {
            if (!array_key_exists($key, $this->original) || $value !== $this->original[$key]) {
                $dirty[$key] = $value;
            }
        }
        
        return $dirty;
    }

    /**
     * Define um atributo
     */
    public function __set(string $key, $value): void
    {
        if ($this->isFillable($key)) {
            $this->setAttribute($key, $value);
        }
    }

    /**
     * Obtém um atributo
     */
    public function __get(string $key)
    {
        return $this->attributes[$key] ?? null;
    }

    /**
     * Verifica se um atributo existe
     */
    public function __isset(string $key): bool
    {
        return isset($this->attributes[$key]);
    }

    /**
     * Converte o modelo para array
     */
    public function toArray(): array
    {
        return $this->attributes;
    }

    /**
     * Converte o modelo para JSON
     */
    public function toJson(): string
    {
        return json_encode($this->toArray());
    }
} 