<?php
// src/Cache/FileCache.php

namespace Hi\Cache;

class FileCache implements CacheInterface
{
    private string $cacheDirectory;

    public function __construct(Config $config)
    {
        $this->cacheDirectory = $config['cache']['directory'];
    }

    public function get(string $key, mixed $default = null): mixed
    {
        $cacheFile = $this->getCacheFilePath($key);

        if (!file_exists($cacheFile)) {
            return $default;
        }

        $data = require $cacheFile;
        return $data['value'] ?? $default;
    }

    public function set(string $key, mixed $value, array $metadata = []): void
    {
        $this->ensureCacheDirectoryExists();

        $cacheData = [
            'value' => $value,
            'metadata' => $metadata,
            'generated_at' => time(),
        ];

        $export = var_export($cacheData, true);
        $content = "<?php\n\nreturn " . $export . ";\n";

        file_put_contents($this->getCacheFilePath($key), $content);
    }

    public function isValid(string $key, array $metadata = []): bool
    {
        $cacheFile = $this->getCacheFilePath($key);

        if (!file_exists($cacheFile)) {
            return false;
        }

        if (empty($metadata)) {
            // No metadata to validate against, cache is valid if it exists
            return true;
        }

        $cachedData = require $cacheFile;
        $cachedMetadata = $cachedData['metadata'] ?? [];

        return $this->validateMetadata($metadata, $cachedMetadata);
    }

    public function delete(string $key): void
    {
        $cacheFile = $this->getCacheFilePath($key);

        if (file_exists($cacheFile)) {
            unlink($cacheFile);
        }
    }

    public function clear(): void
    {
        if (!is_dir($this->cacheDirectory)) {
            return;
        }

        $files = glob($this->cacheDirectory . '/*.php');
        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }
    }

    private function getCacheFilePath(string $key): string
    {
        // Sanitize key to prevent directory traversal
        $sanitizedKey = preg_replace('/[^a-zA-Z0-9_-]/', '_', $key);
        return $this->cacheDirectory . '/' . $sanitizedKey . '.php';
    }

    private function ensureCacheDirectoryExists(): void
    {
        if (!is_dir($this->cacheDirectory)) {
            mkdir($this->cacheDirectory, 0755, true);
        }
    }

    /**
     * Validate metadata against cached metadata
     *
     * Expected metadata structure: ['files' => ['/path/file.php' => mtime, ...]]
     */
    private function validateMetadata(array $currentMetadata, array $cachedMetadata): bool
    {
        $currentFiles = $currentMetadata['files'] ?? [];
        $cachedFiles = $cachedMetadata['files'] ?? [];

        // Check if file lists match (new or removed files)
        if (count($currentFiles) !== count($cachedFiles)) {
            return false;
        }

        // Check each file's modification time
        foreach ($currentFiles as $file => $mtime) {
            if (!isset($cachedFiles[$file])) {
                // New file added
                return false;
            }

            if ($mtime > $cachedFiles[$file]) {
                // File was modified
                return false;
            }
        }

        // Check for removed files
        foreach (array_keys($cachedFiles) as $cachedFile) {
            if (!isset($currentFiles[$cachedFile])) {
                return false;
            }
        }

        return true;
    }
}
