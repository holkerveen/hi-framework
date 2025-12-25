<?php

namespace Hi\Entity;

use Doctrine\ORM\Mapping as ORM;
use Hi\Storage\EntityInterface;

#[ORM\Entity]
#[ORM\Table(name: 'users')]
class User implements EntityInterface
{
    #[ORM\Id]
    #[ORM\Column(type: 'string')]
    private string $id;
    #[ORM\Column]
    private string $email;
    #[ORM\Column]
    private string $password;
    
    public function setId(string $id) {
        $this->id = $id;
    }

    public function setEmail(string $email)
    {
        $this->email = $email;
    }

    public function setPassword(string $password)
    {
        $this->password = password_hash($password, PASSWORD_DEFAULT);
    }
    
    public function getId(): string
    {
        return $this->id;
    }
    
    public function getEmail(): string
    {
        return $this->email;
    }
    
    public function verifyPassword(string $password): bool {
        return password_verify($password, $this->password);
    }
}
