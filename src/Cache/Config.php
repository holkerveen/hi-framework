<?php

namespace Hi\Cache;

use ArrayAccess;
use Exception;
use Hi\PathHelper;

class Config implements ArrayAccess
{
    public function __construct(private ?array $config = null) {
        if(is_null($this->config)) {
            $this->config = require(PathHelper::getBasedir() . DIRECTORY_SEPARATOR . 'config.php');
        }
    }
    public function offsetExists(mixed $offset): bool
    {
        return isset($this->config[$offset]);
    }

    public function offsetGet(mixed $offset): Config | string
    {
        if(is_string($this->config[$offset])) {
            return $this->config[$offset];
        }
        return new Config($this->config[$offset]);
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        throw new Exception("Config is read-only");
    }

    public function offsetUnset(mixed $offset): void
    {
        throw new Exception("Config is read-only");
    }
}
