<?php

namespace Hi\Entity;

use Doctrine\ORM\Mapping as ORM;
use Hi\Storage\EntityInterface;

#[ORM\Entity]
#[ORM\Table(name: 'comments')]
class Comment implements EntityInterface
{
    #[ORM\Id]
    #[ORM\Column(type: 'string')]
    private string $id;
    #[ORM\Column]
    private string $name;
    #[ORM\Column]
    private string $email;
    #[ORM\Column]
    private string $message;
    
    public function setId(string $id) {
        $this->id = $id;
    }

    public function setName(mixed $name)
    {
        $this->name = $name;
    }

    public function setEmail(mixed $email)
    {
        $this->email = $email;
    }

    public function setMessage(mixed $message)
    {
        $this->message = $message;
    }
    
    public function getId(): string
    {
        return $this->id;
    }
    
    public function getName(): string
    {
        return $this->name;
    }
    public function getEmail(): string
    {
        return $this->email;
    }
    public function getMessage(): string
    {
        return $this->message;
    }
}
