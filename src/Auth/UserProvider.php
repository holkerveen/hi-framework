<?php

namespace Hi\Auth;

use Hi\Storage\EntityStorageInterface;

class UserProvider implements UserProviderInterface
{
    public function __construct(private EntityStorageInterface $entityStorage) {
    }

    public function create(): UserInterface
    {
        return new User();
    }

    public function persist(UserInterface $user): static
    {
        $this->entityStorage->create($user);
    }
}
