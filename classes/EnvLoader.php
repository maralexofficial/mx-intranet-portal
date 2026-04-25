<?php

class EnvLoader
{
    protected string $path;
    protected array $env = [];

    public function __construct(string $path = null)
    {
        $this->path = $path ?? __DIR__ . '/.env';
        $this->load();
    }

    protected function load(): void
    {
        if (!file_exists($this->path)) {
            return;
        }

        $lines = file($this->path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        foreach ($lines as $line) {
            $line = trim($line);

            // Kommentare ignorieren
            if ($line === '' || str_starts_with($line, '#')) {
                continue;
            }

            // KEY=VALUE splitten
            [$key, $value] = array_pad(explode('=', $line, 2), 2, null);

            if ($key !== null) {
                $this->env[trim($key)] = trim($value ?? '');
            }
        }
    }

    public function get(string $key, $default = null)
    {
        return $this->env[$key] ?? $default;
    }

    public function all(): array
    {
        return $this->env;
    }

    public function has(string $key): bool
    {
        return isset($this->env[$key]);
    }

    public function bool(string $key, bool $default = false): bool
    {
        if (!isset($this->env[$key])) {
            return $default;
        }

        return filter_var($this->env[$key], FILTER_VALIDATE_BOOLEAN);
    }
}