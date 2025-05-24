<?php

namespace LadyPHP\Database;

use PDO;
use PDOException;

class MigrationManager
{
    private PDO $pdo;
    private string $migrationsPath;
    private string $migrationsTable = 'migrations';

    public function __construct(PDO $pdo, string $migrationsPath)
    {
        $this->pdo = $pdo;
        $this->migrationsPath = $migrationsPath;
        $this->createMigrationsTable();
    }

    /**
     * Cria a tabela de migrações se não existir
     */
    private function createMigrationsTable(): void
    {
        $sql = "CREATE TABLE IF NOT EXISTS {$this->migrationsTable} (
            id INT AUTO_INCREMENT PRIMARY KEY,
            migration VARCHAR(255) NOT NULL,
            batch INT NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )";

        $this->pdo->exec($sql);
    }

    /**
     * Executa todas as migrações pendentes
     */
    public function migrate(): array
    {
        $files = $this->getPendingMigrations();
        if (empty($files)) {
            return ['message' => 'Nothing to migrate.'];
        }

        $batch = $this->getNextBatchNumber();
        $executed = [];

        foreach ($files as $file) {
            try {
                $className = 'Database\\Migrations\\' . $this->getMigrationClassName($file);
                require_once $file;
                
                $migration = new $className($this->pdo);
                $migration->up();

                $this->pdo->prepare("INSERT INTO {$this->migrationsTable} (migration, batch) VALUES (?, ?)")
                    ->execute([basename($file), $batch]);

                $executed[] = basename($file);
            } catch (\Exception $e) {
                throw new \Exception("Error executing migration {$file}: " . $e->getMessage());
            }
        }

        return [
            'message' => count($executed) . ' migrations executed successfully.',
            'migrations' => $executed
        ];
    }

    /**
     * Reverte a última migração executada
     */
    public function rollback(): array
    {
        $batch = $this->getLastBatchNumber();
        if (!$batch) {
            return ['message' => 'Nothing to rollback.'];
        }

        $files = $this->getMigrationsForBatch($batch);
        $rolledBack = [];

        foreach (array_reverse($files) as $file) {
            try {
                $className = 'Database\\Migrations\\' . $this->getMigrationClassName($file);
                require_once $file;
                
                $migration = new $className($this->pdo);
                $migration->down();

                $this->pdo->prepare("DELETE FROM {$this->migrationsTable} WHERE migration = ?")
                    ->execute([basename($file)]);

                $rolledBack[] = basename($file);
            } catch (\Exception $e) {
                throw new \Exception("Error rolling back migration {$file}: " . $e->getMessage());
            }
        }

        return [
            'message' => count($rolledBack) . ' migrations rolled back successfully.',
            'migrations' => $rolledBack
        ];
    }

    /**
     * Cria um novo arquivo de migração
     */
    public function create(string $name): string
    {
        $timestamp = date('Y_m_d_His');
        $className = $this->getMigrationClassNameFromName($name);
        $filename = $timestamp . '_' . $name . '.php';
        $path = $this->migrationsPath . '/' . $filename;

        if (!is_dir($this->migrationsPath)) {
            mkdir($this->migrationsPath, 0755, true);
        }

        $content = <<<PHP
<?php

namespace Database\Migrations;

use LadyPHP\Database\Migration;
use LadyPHP\Database\Blueprint;

class {$className} extends Migration
{
    public function up()
    {
        \$this->schema->create('table_name', function (Blueprint \$table) {
            \$table->id();
            \$table->timestamps();
        });
    }

    public function down()
    {
        \$this->schema->drop('table_name');
    }
}
PHP;

        file_put_contents($path, $content);
        return $path;
    }

    /**
     * Retorna a lista de migrações pendentes
     */
    private function getPendingMigrations(): array
    {
        $files = glob($this->migrationsPath . '/*.php');
        $executed = $this->getExecutedMigrations();
        
        return array_filter($files, function($file) use ($executed) {
            return !in_array(basename($file), $executed);
        });
    }

    /**
     * Retorna a lista de migrações já executadas
     */
    private function getExecutedMigrations(): array
    {
        $stmt = $this->pdo->query("SELECT migration FROM {$this->migrationsTable} ORDER BY id");
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    /**
     * Retorna o número do próximo batch
     */
    private function getNextBatchNumber(): int
    {
        $stmt = $this->pdo->query("SELECT MAX(batch) FROM {$this->migrationsTable}");
        return (int)$stmt->fetchColumn() + 1;
    }

    /**
     * Retorna o número do último batch
     */
    private function getLastBatchNumber(): ?int
    {
        $stmt = $this->pdo->query("SELECT MAX(batch) FROM {$this->migrationsTable}");
        return $stmt->fetchColumn() ?: null;
    }

    /**
     * Retorna as migrações de um batch específico
     */
    private function getMigrationsForBatch(int $batch): array
    {
        $stmt = $this->pdo->prepare("SELECT migration FROM {$this->migrationsTable} WHERE batch = ? ORDER BY id");
        $stmt->execute([$batch]);
        $migrations = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        return array_map(function($migration) {
            return $this->migrationsPath . '/' . $migration;
        }, $migrations);
    }

    /**
     * Converte o nome do arquivo em nome da classe
     */
    private function getMigrationClassName(string $file): string
    {
        // Pega apenas o nome do arquivo sem a extensão
        $name = basename($file, '.php');
        
        // Remove o timestamp (formato: YYYY_MM_DD_HHMMSS_)
        $name = preg_replace('/^\d{4}_\d{2}_\d{2}_\d{6}_/', '', $name);
        
        // Converte snake_case para PascalCase
        $name = str_replace(' ', '', ucwords(str_replace('_', ' ', $name)));
        
        return $name;
    }

    /**
     * Converte o nome da migração em nome da classe
     */
    private function getMigrationClassNameFromName(string $name): string
    {
        // Converte snake_case para PascalCase
        $name = str_replace(' ', '', ucwords(str_replace('_', ' ', $name)));
        
        return $name;
    }
} 