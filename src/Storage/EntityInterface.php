<?php

namespace Framework\Storage;

interface EntityInterface {
    public function setId(string $id);
    public function getId(): string;
}
