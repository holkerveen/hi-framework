<?php

namespace Hi\Storage;

interface EntityInterface {
    public function setId(string $id);
    public function getId(): string;
}
