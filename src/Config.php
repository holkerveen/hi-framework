<?php

namespace Hi;

use ArrayAccess;
use Exception;

class Config implements ArrayAccess
{
    public function __construct(private ?array $config = null, private array $path = [])
    {
        if (is_null($this->config)) {
            $this->config = require(PathHelper::getBasedir() . DIRECTORY_SEPARATOR . 'config.php');
        }
    }

    public function offsetExists(mixed $offset): bool
    {
        return isset($this->config[$offset]);
    }

    public function offsetGet(mixed $offset): Config|string|bool|float|int
    {
        $path = [...$this->path, $offset];
        if(!array_key_exists($offset, $this->config)) {
            throw new Exception("Config option ".implode('.',$path)." does not exist.");
        }
        if (is_array($this->config[$offset])) {
            return new Config($this->config[$offset], $path);
        }
        return $this->config[$offset];
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
