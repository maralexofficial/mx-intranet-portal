<?php

class ComponentLoader
{
    private string $baseDir;
    private string $default;
    private array $components = [];

    public function __construct(string $baseDir, string $default = 'default')
    {
        $this->baseDir = rtrim($baseDir, '/') . '/';
        $this->default = $default;

        $this->scanComponents();
    }

    private function scanComponents(): void
    {
        foreach (scandir($this->baseDir) as $folder) {
            if ($folder === '.' || $folder === '..')
                continue;

            $path = $this->baseDir . $folder . '/index.php';

            if (is_dir($this->baseDir . $folder) && file_exists($path)) {
                $this->components[] = $folder;
            }
        }
    }

    private function normalize(string $name): string
    {
        $name = strtolower($name);
        $name = preg_replace('/[^a-z0-9\-]/', '', $name);

        // optional: simple auto slug fix
        $name = str_replace(' ', '-', $name);

        return $name;
    }

    public function load(?string $input): void
    {
        $input = $this->normalize($input ?? $this->default);

        // fallback wenn nicht erlaubt
        if (!in_array($input, $this->components)) {
            $input = $this->default;
        }

        $path = $this->baseDir . $input . '/index.php';

        // finaler safety check
        $realBase = realpath($this->baseDir);
        $realPath = realpath($path);

        if (!$realPath || !str_starts_with($realPath, $realBase)) {
            $realPath = $this->baseDir . $this->default . '/index.php';
        }

        require $realPath;
    }
}
