<?php
// src/Controllers/UserController.php

namespace Hi\Controllers;

use Hi\Attributes\AllowAccess;
use Hi\Attributes\Route;
use Hi\Entity\User;
use Hi\Enums\Role;
use Hi\Storage\EntityStorageInterface;
use Hi\ViewInterface;

class UserController
{
    #[Route('/users')]
    #[AllowAccess(Role::Authenticated)]
    public function index(EntityStorageInterface $store, ViewInterface $view): string
    {
        return $view->render("users/index.html.twig", [
            'users' => $store->index(User::class),
        ]);
    }

    #[Route('/users/create')]
    #[AllowAccess(Role::Authenticated)]
    public function create(ViewInterface $view): string
    {
        return $view->render("users/create.html.twig");
    }

    #[Route('/users/post')]
    #[AllowAccess(Role::Authenticated)]
    public function post(EntityStorageInterface $store, ViewInterface $view): string
    {
        $user = new User();
        $user->setEmail($_POST['email']);
        $user->setPassword($_POST['password']);
        $store->create($user);

        return $view->render("users/post.html.twig");
    }

    #[Route('/users/{id}/confirm-delete')]
    #[AllowAccess(Role::Authenticated)]
    public function confirmDelete(string $id, EntityStorageInterface $store, ViewInterface $view): string
    {
        $user = $store->read(User::class, $id);
        return $view->render("users/confirm-delete.html.twig", compact("user"));
    }

    #[Route('/users/{id}/delete')]
    #[AllowAccess(Role::Authenticated)]
    public function delete(string $id, EntityStorageInterface $store, ViewInterface $view): string
    {
        $user = $store->read(User::class, $id);
        $store->delete($user);
        return $view->render("users/delete.html.twig", compact("user"));
    }

    #[Route('/users/{id}/edit')]
    #[AllowAccess(Role::Authenticated)]
    public function edit(string $id, EntityStorageInterface $store, ViewInterface $view): string
    {
        return $view->render("users/edit.html.twig", [
            'user' => $store->read(User::class, $id),
        ]);
    }

    #[Route('/users/{id}/put')]
    #[AllowAccess(Role::Authenticated)]
    public function put(string $id, EntityStorageInterface $store, ViewInterface $view): string
    {
        $user = $store->read(User::class, $id);
        $user->setEmail($_POST['email']);
        if(!empty($_POST['password'])) {
            $user->setPassword($_POST['password']);
        }
        $store->update($user);
        return $view->render("users/put.html.twig", compact("user"));
    }
}