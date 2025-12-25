<?php

namespace Hi\Console;

class ScaffoldCommand
{
    private string $basePath;

    public function __construct()
    {
        $this->basePath = $this->getBasePath();
    }

    public function install(): void
    {
        echo "Installing Hi Framework...\n\n";

        $this->createDirectories();

        // Scaffold files with optional transformations
        $this->scaffoldFile('public/index.php', 'public/index.php');

        $this->scaffoldFile('doctrine-bootstrap.php', 'src/config/doctrine-bootstrap.php', [
            'require_once __DIR__."/vendor/autoload.php";' => 'require_once __DIR__ . \'/../../vendor/autoload.php\';',
            '[__DIR__."/src/Entity"]' => '[__DIR__ . \'/../Entity\']',
            '__DIR__ . \'/db.sqlite\'' => '__DIR__ . \'/../../db.sqlite\'',
        ]);

        $this->scaffoldFile('src/Controllers/HomeController.php', 'src/Controllers/HomeController.php', [
            'namespace Hi\Controllers;' => 'namespace App\Controllers;',
        ]);

        $this->scaffoldFile('templates/scaffold-base.html.twig', 'templates/base.html.twig');
        $this->scaffoldFile('templates/scaffold-home.html.twig', 'templates/home.html.twig');

        echo "\n✅ Installation complete!\n\n";
        echo "Next steps:\n";
        echo "  1. Configure your database in src/config/doctrine-bootstrap.php\n";
        echo "  2. Start the development server: composer run framework:serve\n";
        echo "  3. Visit http://localhost:8000\n\n";
    }

    public function serve(): void
    {
        $publicPath = $this->basePath . '/public';

        if (!is_dir($publicPath)) {
            echo "❌ Error: public/ directory not found.\n";
            echo "Run 'composer run framework:install' first.\n";
            exit(1);
        }

        echo "Starting PHP development server...\n";
        echo "Server running at http://localhost:8000\n";
        echo "Press Ctrl+C to stop.\n\n";

        passthru("php -S localhost:8000 -t " . escapeshellarg($publicPath));
    }

    private function createDirectories(): void
    {
        $directories = [
            'public',
            'src/Controllers',
            'src/Entity',
            'src/config',
            'templates',
        ];

        foreach ($directories as $dir) {
            $path = $this->basePath . '/' . $dir;
            if (!is_dir($path)) {
                mkdir($path, 0755, true);
                echo "✓ Created {$dir}/\n";
            }
        }
    }

    private function scaffoldFile(string $source, string $target, array $replacements = []): void
    {
        $targetFile = $this->basePath . '/' . $target;
        if (file_exists($targetFile)) {
            echo "⊙ {$target} already exists, skipping\n";
            return;
        }

        $sourceFile = $this->getFrameworkPath() . '/' . $source;
        if (!file_exists($sourceFile)) {
            echo "❌ Error: Source file {$source} not found\n";
            return;
        }

        $content = file_get_contents($sourceFile);

        // Apply any replacements
        if (!empty($replacements)) {
            $content = str_replace(array_keys($replacements), array_values($replacements), $content);
        }

        file_put_contents($targetFile, $content);
        echo "✓ Created {$target}\n";
    }

    private function getBasePath(): string
    {
        if (str_contains(__DIR__, 'vendor/holkerveen/hi-framework')) {
            return dirname(__DIR__, 3);
        }

        return dirname(__DIR__, 2);
    }

    private function getFrameworkPath(): string
    {
        return dirname(__DIR__, 2);
    }
}
