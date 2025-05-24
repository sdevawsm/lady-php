<?php

namespace LadyPHP\Console\Commands;

use LadyPHP\Console\Command;
use LadyPHP\Database\Config;
use LadyPHP\Database\MigrationManager;
use PDO;

class MigrateCommand extends Command
{
    protected string $name = 'migrate';
    protected string $description = 'Run database migrations';

    private MigrationManager $manager;

    public function __construct()
    {
        try {
            Config::load();
            $pdo = new PDO(
                Config::getDsn(),
                Config::getCredentials()['username'],
                Config::getCredentials()['password'],
                Config::getPdoOptions()
            );

            $this->manager = new MigrationManager($pdo, __DIR__ . '/../../../database/migrations');
        } catch (\Exception $e) {
            $this->error("Database configuration error: " . $e->getMessage());
            exit(1);
        }
    }

    public function handle(): void
    {
        try {
            $result = $this->manager->migrate();
            $this->success($result['message']);
            
            if (!empty($result['migrations'])) {
                $this->info("Migrations executed:");
                foreach ($result['migrations'] as $migration) {
                    $this->info("- {$migration}");
                }
            }
        } catch (\Exception $e) {
            $this->error("Error: " . $e->getMessage());
        }
    }

    private function rollback(): void
    {
        try {
            $result = $this->manager->rollback();
            $this->success($result['message']);
            
            if (!empty($result['migrations'])) {
                $this->info("Migrations rolled back:");
                foreach ($result['migrations'] as $migration) {
                    $this->info("- {$migration}");
                }
            }
        } catch (\Exception $e) {
            $this->error("Error: " . $e->getMessage());
        }
    }

    private function make(string $name): void
    {
        try {
            $path = $this->manager->create($name);
            $this->success("Migration created: {$path}");
        } catch (\Exception $e) {
            $this->error("Error: " . $e->getMessage());
        }
    }

    private function showHelp(): void
    {
        echo "\nMigration Commands:\n\n";
        echo "  migrate          Run all pending migrations\n";
        echo "  rollback         Rollback the last migration batch\n";
        echo "  make <name>      Create a new migration file\n\n";
    }
} 