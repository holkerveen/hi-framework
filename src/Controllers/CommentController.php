<?php
// src/Controllers/CommentController.php

namespace Framework\Controllers;

use Framework\Attributes\Route;
use Framework\Entity\Comment;
use Framework\Storage\EntityStorageInterface;
use Twig\Environment;

class CommentController
{
    #[Route('/comments')]
    public function index(EntityStorageInterface $store, Environment $twig): string
    {
        $comments = $store->index(Comment::class);
        $count = count($store->index(Comment::class));
        return $twig->render("comments/index.html.twig", compact("comments", "count"));
    }
    
    #[Route('/comments/create')]
    public function create(Environment $twig): string
    {
        return $twig->render("comments/create.html.twig");
    }
    
    #[Route('/comments/post')]
    public function post(EntityStorageInterface $store, Environment $twig): string {
        $comment = new Comment();
        $comment->setName($_POST['name']);
        $comment->setEmail($_POST['email']);
        $comment->setMessage($_POST['message']);
        $store->create($comment);
        
        return $twig->render("comments/post.html.twig");
    }
    
    #[Route('/comments/{id}/edit')]
    public function edit(string $id, EntityStorageInterface $store, Environment $twig): string {
        $comment = $store->read(Comment::class, $id);
        return $twig->render("comments/edit.html.twig", compact("comment"));
    }
    
    #[Route('/comments/{id}/store')]
    public function store(string $id, EntityStorageInterface $store, Environment $twig): string {
        $comment = $store->read(Comment::class, $id);
        $comment->setName($_POST['name']);
        $comment->setEmail($_POST['email']);
        $comment->setMessage($_POST['message']);
        $store->update($comment);
        return $twig->render("comments/store.html.twig", compact("comment"));
    }
    
    #[Route('/comments/{id}/confirm-delete')]
    public function confirmDelete(string $id, EntityStorageInterface $store, Environment $twig): string {
        $comment = $store->read(Comment::class, $id);
        return $twig->render("comments/confirm-delete.html.twig", compact("comment"));
    }

    #[Route('/comments/{id}/delete')]
    public function delete(string $id, EntityStorageInterface $store, Environment $twig): string {
        $store->delete($store->read(Comment::class, $id));
        return $twig->render("comments/delete.html.twig");
    }
}