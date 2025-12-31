<?php

namespace Hi;

use Hi\Entity\User;

/**
 * Plain PHP object for storing user data in sessions
 * Does not contain Doctrine dependencies, so it can be safely serialized
 */
class SessionUser
{
    public function __construct(
        public readonly string $id,
        public readonly string $email,
    ) {
    }

    /**
     * Create a SessionUser from a User entity
     */
    public static function fromUser(User $user): self
    {
        return new self(
            id: $user->getId(),
            email: $user->getEmail(),
        );
    }
}
