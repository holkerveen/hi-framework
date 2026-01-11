<?php

namespace Hi\Auth;

use Doctrine\ORM\Mapping as ORM;
use Hi\Storage\EntityInterface;

#[ORM\Entity]
#[ORM\Table(name: 'users')]
class User implements UserInterface
{
    #[ORM\Id]
    #[ORM\Column(type: 'string')]
    private string $id;
    #[ORM\Column]
    private string $email;
    #[ORM\Column]
    private string $password;

    public function setId(string $id)
    {
        $this->id = $id;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function verifyClientSecret(string $clientSecret): bool
    {
        return password_verify($clientSecret, $this->password);
    }

    public function getClientIdentifier(): string
    {
        return $this->email;
    }

    public function setClientIdentifier(string $clientIdentifier): static
    {
        $this->email = $clientIdentifier;
        return $this;
    }


    public function setClientSecret(string $clientSecret): static
    {
        $this->password = password_hash($clientSecret, PASSWORD_DEFAULT);
        return $this;
    }
}
