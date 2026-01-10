<?php

namespace Hi;

use Composer\Autoload\ClassLoader;
use ReflectionClass;
use RuntimeException;

class PathHelper
{
    private static ?string $basedir = null;

    /**
     * Get the project root directory (basedir)
     *
     * This method reliably finds the project root by checking where
     * the vendor package is installed and traversing the directory structure.
     *
     * Works regardless of:
     * - Whether the vendor package is symlinked
     * - Whether called from within the vendor package
     * - Whether called from the project itself
     * - Whether run as CLI or through composer serve
     *
     * @return string The absolute path to the project root directory
     * @throws RuntimeException If the project root cannot be determined
     */
    public static function getBasedir(): string
    {
        if (self::$basedir !== null) {
            return self::$basedir;
        }

        // Strategy 1: Check if we can find the project root via the vendor autoloader
        // The vendor autoloader is always in vendor/autoload.php relative to project root
        $reflection = new ReflectionClass(ClassLoader::class);
        $vendorDir = dirname($reflection->getFileName(), 2);
        $projectRoot = dirname($vendorDir);

        if (self::isProjectRoot($projectRoot)) {
            self::$basedir = $projectRoot;
            return self::$basedir;
        }

        // Strategy 2: Start from this file's directory (without resolving symlinks)
        // to find vendor directory structure
        $currentDir = __DIR__;

        // Check if we're inside a vendor directory structure
        // Pattern: .../vendor/holkerveen/hi-framework/src
        while ($currentDir !== '/' && $currentDir !== dirname($currentDir)) {
            // Check if current path contains /vendor/holkerveen/hi-framework
            if (preg_match('#^(.*)/vendor/holkerveen/hi-framework(/|$)#', $currentDir, $matches)) {
                $potentialRoot = $matches[1];
                if (self::isProjectRoot($potentialRoot)) {
                    self::$basedir = $potentialRoot;
                    return self::$basedir;
                }
            }
            $currentDir = dirname($currentDir);
        }

        // Strategy 3: Traverse from resolved realpath (handles symlinks)
        $currentDir = realpath(__DIR__);
        if ($currentDir !== false) {
            while ($currentDir !== '/') {
                if (self::isProjectRoot($currentDir)) {
                    self::$basedir = $currentDir;
                    return self::$basedir;
                }

                $parentDir = dirname($currentDir);
                if ($parentDir === $currentDir) {
                    break;
                }
                $currentDir = $parentDir;
            }
        }

        // Strategy 4: Check current working directory
        $cwd = getcwd();
        if ($cwd !== false && self::isProjectRoot($cwd)) {
            self::$basedir = $cwd;
            return self::$basedir;
        }

        throw new RuntimeException(
            'Could not determine project root directory. ' .
            'Make sure composer.json requires "holkerveen/hi-framework".'
        );
    }

    /**
     * Check if a directory is the project root
     *
     * @param string $dir Directory to check
     * @return bool True if directory appears to be the project root
     */
    private static function isProjectRoot(string $dir): bool
    {
        $composerJsonPath = $dir . '/composer.json';

        if (!file_exists($composerJsonPath)) {
            return false;
        }

        $composerContent = file_get_contents($composerJsonPath);
        if ($composerContent === false) {
            return false;
        }

        $composerData = json_decode($composerContent, true);
        if ($composerData === null) {
            return false;
        }

        // Check if this composer.json requires hi-framework
        // (indicating it's a project using the framework, not the framework itself)
        if (!isset($composerData['require']['holkerveen/hi-framework'])) {
            return false;
        }

        // Verify this looks like a project root (has vendor dir)
        if (!is_dir($dir . '/vendor')) {
            return false;
        }

        return true;
    }

    /**
     * Reset the cached basedir (useful for testing)
     */
    public static function reset(): void
    {
        self::$basedir = null;
    }
}
