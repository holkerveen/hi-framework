<?php

namespace Hi\Security;

use Hi\Attributes\AllowAccess;
use Hi\Enums\Role;
use Hi\SessionInterface;
use ReflectionMethod;

class AccessControl
{
    public function __construct(private SessionInterface $session)
    {
    }

    public function isAllowed(object $controller, string $methodName): bool
    {
        $reflection = new ReflectionMethod($controller, $methodName);
        $attributes = $reflection->getAttributes(AllowAccess::class);

        // Secure by default: deny access if no AllowAccess attribute is present
        if (empty($attributes)) {
            return false;
        }

        $allowAccess = $attributes[0]->newInstance();
        $isAuthenticated = $this->session->has('user');

        // Check if the required role matches the user's authentication status
        if ($allowAccess->role === Role::Authenticated && !$isAuthenticated) {
            return false;
        }

        return true;
    }
}
