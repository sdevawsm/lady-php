<?php

namespace LadyPHP\Console;

class Application
{
    private array $commands = [];
    private string $name = 'LadyPHP Console';
    private string $version = '1.0.0';

    /**
     * Registra um novo comando
     */
    public function add(Command $command): void
    {
        $this->commands[$command->getName()] = $command;
    }

    /**
     * Executa o comando especificado
     */
    public function run(array $argv): void
    {
        // Remove o nome do script
        array_shift($argv);

        // Se não houver argumentos, mostra a ajuda
        if (empty($argv)) {
            $this->showHelp();
            return;
        }

        $command = $argv[0];
        array_shift($argv);

        // Processa argumentos e opções
        $arguments = [];
        $options = [];

        foreach ($argv as $arg) {
            if (str_starts_with($arg, '--')) {
                // Opção com valor (--option=value)
                if (str_contains($arg, '=')) {
                    [$name, $value] = explode('=', substr($arg, 2), 2);
                    $options[$name] = $value;
                } else {
                    // Opção booleana (--option)
                    $options[substr($arg, 2)] = true;
                }
            } elseif (str_starts_with($arg, '-')) {
                // Opção curta (-o=value ou -o value)
                $name = substr($arg, 1);
                if (str_contains($name, '=')) {
                    [$name, $value] = explode('=', $name, 2);
                    $options[$name] = $value;
                } else {
                    $options[$name] = true;
                }
            } else {
                // Argumento normal
                $arguments[] = $arg;
            }
        }

        // Procura o comando pelo nome
        if (isset($this->commands[$command])) {
            $cmd = $this->commands[$command];
            $cmd->setArguments($arguments);
            $cmd->setOptions($options);
            $cmd->handle();
            return;
        }

        $this->error("Command '{$command}' not found.");
        $this->showHelp();
    }

    /**
     * Mostra a mensagem de ajuda
     */
    private function showHelp(): void
    {
        echo "\n\033[1m{$this->name} v{$this->version}\033[0m\n\n";
        echo "Usage:\n";
        echo "  php lady <command> [options] [arguments]\n\n";
        echo "Available commands:\n";

        foreach ($this->commands as $name => $command) {
            echo "  {$name}\n";
        }

        echo "\n";
    }

    /**
     * Escreve uma mensagem de erro
     */
    private function error(string $message): void
    {
        echo "\033[31m{$message}\033[0m\n";
    }
} 