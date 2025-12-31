<?php
// src/Session.php

namespace Hi;

class Session implements SessionInterface
{
    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function get(string $key, mixed $default = null): mixed
    {
        return $_SESSION[$key] ?? $default;
    }

    public function set(string $key, mixed $value): void
    {
        $_SESSION[$key] = $value;
    }

    public function has(string $key): bool
    {
        return isset($_SESSION[$key]);
    }

    public function remove(string $key): void
    {
        unset($_SESSION[$key]);
    }

    public function clear(): void
    {
        session_destroy();
        $_SESSION = [];
    }

    /**
     * Magic method to allow property-style access in templates
     * e.g., $session->user instead of $session->get('user')
     */
    public function __get(string $key): mixed
    {
        return $this->get($key);
    }

    /**
     * Magic method to check if a property exists in templates
     * e.g., isset($session->user)
     */
    public function __isset(string $key): bool
    {
        return $this->has($key);
    }
}
