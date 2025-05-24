<?php

namespace LadyPHP\Console\Commands;

use LadyPHP\Console\Command;
use LadyPHP\Database\Config\Config;
use LadyPHP\Database\Migration\MigrationManager;
use PDO;

class MakeMigrationCommand extends Command
{
    protected string $name = 'make:migration';
    protected string $description = 'Create a new migration file';

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
        $name = $this->argument(0);
        if (!$name) {
            $this->error("Migration name is required.");
            $this->showHelp();
            return;
        }

        try {
            $path = $this->manager->create($name);
            $this->success("Migration created: {$path}");
        } catch (\Exception $e) {
            $this->error("Error: " . $e->getMessage());
        }
    }

    private function showHelp(): void
    {
        echo "\nUsage:\n";
        echo "  lady make:migration <name>\n\n";
        echo "Arguments:\n";
        echo "  name              The name of the migration\n\n";
        echo "Example:\n";
        echo "  lady make:migration create_users_table\n\n";
    }
} 