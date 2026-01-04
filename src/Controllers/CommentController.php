<?php
// src/Controllers/CommentController.php

namespace Hi\Controllers;

use Hi\Attributes\AllowAccess;
use Hi\Attributes\Route;
use Hi\Entity\Comment;
use Hi\Enums\Role;
use Hi\Storage\EntityStorageInterface;
use Hi\ViewInterface;

class CommentController
{
    #[Route('/comments')]
    #[AllowAccess(Role::Unauthenticated)]
    public function index(EntityStorageInterface $store, ViewInterface $view): string
    {
        $comments = $store->index(Comment::class);
        $count = count($store->index(Comment::class));
        return $view->render("comments/index.html.twig", compact("comments", "count"));
    }

    #[Route('/comments/create')]
    #[AllowAccess(Role::Unauthenticated)]
    public function create(ViewInterface $view): string
    {
        return $view->render("comments/create.html.twig");
    }

    #[Route('/comments/post')]
    #[AllowAccess(Role::Unauthenticated)]
    public function post(EntityStorageInterface $store, ViewInterface $view): string {
        $comment = new Comment();
        $comment->setName($_POST['name']);
        $comment->setEmail($_POST['email']);
        $comment->setMessage($_POST['message']);
        $store->create($comment);

        return $view->render("comments/post.html.twig");
    }

    #[Route('/comments/{id}/edit')]
    #[AllowAccess(Role::Unauthenticated)]
    public function edit(string $id, EntityStorageInterface $store, ViewInterface $view): string {
        $comment = $store->read(Comment::class, $id);
        return $view->render("comments/edit.html.twig", compact("comment"));
    }

    #[Route('/comments/{id}/store')]
    #[AllowAccess(Role::Unauthenticated)]
    public function store(string $id, EntityStorageInterface $store, ViewInterface $view): string {
        $comment = $store->read(Comment::class, $id);
        $comment->setName($_POST['name']);
        $comment->setEmail($_POST['email']);
        $comment->setMessage($_POST['message']);
        $store->update($comment);
        return $view->render("comments/store.html.twig", compact("comment"));
    }

    #[Route('/comments/{id}/confirm-delete')]
    #[AllowAccess(Role::Unauthenticated)]
    public function confirmDelete(string $id, EntityStorageInterface $store, ViewInterface $view): string {
        $comment = $store->read(Comment::class, $id);
        return $view->render("comments/confirm-delete.html.twig", compact("comment"));
    }

    #[Route('/comments/{id}/delete')]
    #[AllowAccess(Role::Unauthenticated)]
    public function delete(string $id, EntityStorageInterface $store, ViewInterface $view): string {
        $store->delete($store->read(Comment::class, $id));
        return $view->render("comments/delete.html.twig");
    }
}