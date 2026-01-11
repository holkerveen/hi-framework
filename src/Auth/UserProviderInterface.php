<?php

namespace Hi\Auth;

interface UserProviderInterface {
    public function create(): UserInterface;

    public function persist(UserInterface $user): static;
}
