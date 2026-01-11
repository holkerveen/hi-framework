<?php

namespace Hi\Auth;

use Hi\Storage\EntityInterface;

interface UserInterface extends EntityInterface
{
    public function getId(): string;

    public function getClientIdentifier(): string;

    public function setClientIdentifier(string $clientIdentifier): static;

    public function verifyClientSecret(string $clientSecret): bool;

    public function setClientSecret(string $clientSecret): static;
}
