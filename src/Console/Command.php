<?php

namespace LadyPHP\Console;

abstract class Command
{
    protected array $arguments = [];
    protected array $options = [];
    protected string $name = '';
    protected string $description = '';

    /**
     * Retorna o nome do comando
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Executa o comando
     */
    abstract public function handle(): void;

    /**
     * Define os argumentos do comando
     */
    public function setArguments(array $arguments): void
    {
        $this->arguments = $arguments;
    }

    /**
     * Define as opções do comando
     */
    public function setOptions(array $options): void
    {
        $this->options = $options;
    }

    /**
     * Retorna um argumento pelo índice
     */
    protected function argument(int $index, $default = null)
    {
        return $this->arguments[$index] ?? $default;
    }

    /**
     * Retorna uma opção pelo nome
     */
    protected function option(string $name, $default = null)
    {
        return $this->options[$name] ?? $default;
    }

    /**
     * Escreve uma mensagem no console
     */
    protected function info(string $message): void
    {
        echo "\033[32m{$message}\033[0m\n";
    }

    /**
     * Escreve uma mensagem de erro no console
     */
    protected function error(string $message): void
    {
        echo "\033[31m{$message}\033[0m\n";
    }

    /**
     * Escreve uma mensagem de aviso no console
     */
    protected function warn(string $message): void
    {
        echo "\033[33m{$message}\033[0m\n";
    }

    /**
     * Escreve uma mensagem de sucesso no console
     */
    protected function success(string $message): void
    {
        echo "\033[36m{$message}\033[0m\n";
    }
} 