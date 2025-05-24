<?php

namespace LadyPHP\Console\Commands;

use LadyPHP\Console\Command;
use LadyPHP\Database\Config\Config;
use LadyPHP\Database\Migration\MigrationManager;
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
        $command = $this->argument(0);
        
        // Se não houver subcomando, executa migrate por padrão
        if (empty($command)) {
            $this->migrate();
            return;
        }

        // Remove o prefixo 'migrate:' se existir
        $command = str_replace('migrate:', '', $command);

        switch ($command) {
            case 'migrate':
                $this->migrate();
                break;
            
            case 'rollback':
                $steps = (int)($this->argument(1) ?? 1);
                $this->rollback($steps);
                break;
            
            case 'fresh':
                $this->fresh();
                break;
            
            case 'refresh':
                $this->refresh();
                break;
            
            case 'reset':
                $this->reset();
                break;
            
            case 'status':
                $this->status();
                break;
            
            default:
                $this->showHelp();
        }
    }

    private function migrate(): void
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

    private function rollback(int $steps = 1): void
    {
        try {
            $result = $this->manager->rollback($steps);
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

    private function fresh(): void
    {
        try {
            $this->info("Dropping all tables...");
            $this->manager->dropAllTables();
            
            $this->info("Running all migrations...");
            $this->migrate();
            
            $this->success("Database has been recreated successfully.");
        } catch (\Exception $e) {
            $this->error("Error: " . $e->getMessage());
        }
    }

    private function refresh(): void
    {
        try {
            $this->info("Rolling back all migrations...");
            $this->manager->rollbackAll();
            
            $this->info("Running all migrations...");
            $this->migrate();
            
            $this->success("Database has been refreshed successfully.");
        } catch (\Exception $e) {
            $this->error("Error: " . $e->getMessage());
        }
    }

    private function reset(): void
    {
        try {
            $result = $this->manager->rollbackAll();
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

    private function status(): void
    {
        try {
            $status = $this->manager->getMigrationStatus();
            
            if (empty($status)) {
                $this->info("No migrations found.");
                return;
            }

            $this->info("\nMigration Status:");
            $this->info("----------------");
            
            foreach ($status as $migration) {
                $status = $migration['ran'] ? "\033[32m✓ Ran\033[0m" : "\033[31m✗ Pending\033[0m";
                $this->info(sprintf(
                    "%s %s",
                    $status,
                    $migration['migration']
                ));
            }
        } catch (\Exception $e) {
            $this->error("Error: " . $e->getMessage());
        }
    }

    private function showHelp(): void
    {
        echo "\nMigration Commands:\n\n";
        echo "  migrate              Run all pending migrations\n";
        echo "  migrate:rollback     Rollback the last migration batch\n";
        echo "  migrate:fresh        Drop all tables and re-run all migrations\n";
        echo "  migrate:refresh      Rollback all migrations and re-run them\n";
        echo "  migrate:reset        Rollback all migrations\n";
        echo "  migrate:status       Show the status of each migration\n\n";
        echo "Options:\n";
        echo "  --steps=<number>     The number of migrations to rollback\n\n";
    }
} 