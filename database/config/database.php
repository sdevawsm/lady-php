<?php

namespace Database\Config;

use PDO;

class Database
{
    private static array $config = [];
    private static bool $loaded = false;

    /**
     * Carrega as configurações do arquivo .env
     */
    public static function load(): void
    {
        if (self::$loaded) {
            return;
        }

        $envFile = __DIR__ . '/../../.env';
        if (!file_exists($envFile)) {
            throw new \Exception('.env file not found. Please create one based on .env.example');
        }

        $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            if (strpos($line, '=') !== false && strpos($line, '#') !== 0) {
                [$key, $value] = explode('=', $line, 2);
                $key = trim($key);
                $value = trim($value);
                
                // Remove aspas se existirem
                if (strpos($value, '"') === 0 || strpos($value, "'") === 0) {
                    $value = substr($value, 1, -1);
                }
                
                self::$config[$key] = $value;
            }
        }

        self::$loaded = true;
    }

    /**
     * Retorna uma configuração específica
     */
    public static function get(string $key, $default = null)
    {
        if (!self::$loaded) {
            self::load();
        }

        return self::$config[$key] ?? $default;
    }

    /**
     * Retorna a string de conexão DSN para o PDO
     */
    public static function getDsn(): string
    {
        if (!self::$loaded) {
            self::load();
        }

        $driver = self::get('DB_CONNECTION', 'mysql');
        $host = self::get('DB_HOST', 'localhost');
        $port = self::get('DB_PORT', '3306');
        $database = self::get('DB_DATABASE');

        if (!$database) {
            throw new \Exception('Database name not configured');
        }

        return "{$driver}:host={$host};port={$port};dbname={$database}";
    }

    /**
     * Retorna as credenciais do banco de dados
     */
    public static function getCredentials(): array
    {
        if (!self::$loaded) {
            self::load();
        }

        return [
            'username' => self::get('DB_USERNAME', 'root'),
            'password' => self::get('DB_PASSWORD', '')
        ];
    }

    /**
     * Retorna as opções padrão do PDO
     */
    public static function getPdoOptions(): array
    {
        return [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci"
        ];
    }

    /**
     * Cria uma nova conexão PDO
     */
    public static function createConnection(): PDO
    {
        return new PDO(
            self::getDsn(),
            self::getCredentials()['username'],
            self::getCredentials()['password'],
            self::getPdoOptions()
        );
    }
} 