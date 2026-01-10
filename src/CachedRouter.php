<?php
// src/CachedRouter.php

namespace Hi;

use Hi\Cache\CacheInterface;

class CachedRouter extends Router
{
    private const string CACHE_KEY = 'routes';

    public function __construct(private CacheInterface $cache)
    {
        $controllerFiles = $this->getControllerFiles();
        $metadata = $this->buildMetadata($controllerFiles);

        if ($this->cache->isValid(self::CACHE_KEY, $metadata)) {
            $this->routes = $this->cache->get(self::CACHE_KEY, []);
        } else {
            parent::__construct();
            $this->cache->set(self::CACHE_KEY, $this->routes, $metadata);
        }
    }

    private function getControllerFiles(): array
    {
        return array_unique(array_merge(
            glob(PathHelper::getBasedir() . '/src/Controllers/*.php'),
            glob(__DIR__ . '/Controllers/*.php'),
        ));
    }

    private function buildMetadata(array $controllerFiles): array
    {
        $files = [];
        foreach ($controllerFiles as $file) {
            $files[$file] = filemtime($file);
        }

        return ['files' => $files];
    }
}
