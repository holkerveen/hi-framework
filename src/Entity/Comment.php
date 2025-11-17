<?php

namespace Framework\Entity;

use Framework\Storage\EntityInterface;

class Comment implements EntityInterface
{
    private string $id;
    private string $name;
    private string $email;
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
