<?php

namespace Hi;

use Hi\Auth\UserInterface;

/**
 * Plain PHP object for storing user data in sessions
 * Does not contain Doctrine dependencies, so it can be safely serialized
 */
class SessionUser
{
    public function __construct(
        public readonly string $id,
        public readonly string $clientIdentifier,
        public readonly string $clientSecret,
    ) {
    }

    /**
     * Create a SessionUser from a User entity
     */
    public static function fromUser(UserInterface $user): self
    {
        return new self(
            id: $user->getId(),
            clientIdentifier: $user->getClientIdentifier(),
            clientSecret: $user->getClientSecret(),
        );
    }
}
