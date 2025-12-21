<?php
// src/Controllers/UserController.php

namespace Framework\Controllers;

use Framework\Attributes\AllowAccess;
use Framework\Attributes\Route;
use Framework\Entity\User;
use Framework\Enums\Role;
use Framework\Storage\EntityStorageInterface;
use Twig\Environment;

class UserController
{
    #[Route('/users')]
    #[AllowAccess(Role::Authenticated)]
    public function index(EntityStorageInterface $store, Environment $twig): string
    {
        return $twig->render("users/index.html.twig", [
            'users' => $store->index(User::class),
        ]);
    }

    #[Route('/users/create')]
    #[AllowAccess(Role::Authenticated)]
    public function create(Environment $twig): string
    {
        return $twig->render("users/create.html.twig");
    }

    #[Route('/users/post')]
    #[AllowAccess(Role::Authenticated)]
    public function post(EntityStorageInterface $store, Environment $twig): string
    {
        $user = new User();
        $user->setEmail($_POST['email']);
        $user->setPassword($_POST['password']);
        $store->create($user);

        return $twig->render("users/post.html.twig");
    }

    #[Route('/users/{id}/confirm-delete')]
    #[AllowAccess(Role::Authenticated)]
    public function confirmDelete(string $id, EntityStorageInterface $store, Environment $twig): string
    {
        $user = $store->read(User::class, $id);
        return $twig->render("users/confirm-delete.html.twig", compact("user"));
    }
    
    #[Route('/users/{id}/delete')]
    #[AllowAccess(Role::Authenticated)]
    public function delete(string $id, EntityStorageInterface $store, Environment $twig): string
    {
        $user = $store->read(User::class, $id);
        $store->delete($user);
        return $twig->render("users/delete.html.twig", compact("user"));
    }
    
    #[Route('/users/{id}/edit')]
    #[AllowAccess(Role::Authenticated)]
    public function edit(string $id, EntityStorageInterface $store, Environment $twig): string
    {
        return $twig->render("users/edit.html.twig", [
            'user' => $store->read(User::class, $id),
        ]);
    }
    
    #[Route('/users/{id}/put')]
    #[AllowAccess(Role::Authenticated)]
    public function put(string $id, EntityStorageInterface $store, Environment $twig): string
    {
        $user = $store->read(User::class, $id);
        $user->setEmail($_POST['email']);
        if(!empty($_POST['password'])) {
            $user->setPassword($_POST['password']);
        }
        $store->update($user);
        return $twig->render("users/put.html.twig", compact("user"));
    }
}