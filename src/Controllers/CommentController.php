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
    public function edit(string $id, EntityStorageInterface $store): string {
        $comment = $store->read(Comment::class, $id);
        return "<form action='/comments/".htmlspecialchars($id, ENT_QUOTES)."/store' method='post'>
            <label style='display:block;'>Name<input name='name' value='".htmlspecialchars($comment->getName(), ENT_QUOTES)."'/></label>
            <label style='display:block;'>Email<input name='email' value='".htmlspecialchars($comment->getEmail(), ENT_QUOTES)."'/></label>
            <label style='display:block;'>Message<textarea name='message'/>".htmlspecialchars($comment->getMessage(),ENT_QUOTES)."</textarea></label>
            <button>Save</button>
            </form>";
    }
    
    #[Route('/comments/{id}/store')]
    public function store(string $id, EntityStorageInterface $store): string {
        $comment = $store->read(Comment::class, $id);
        $comment->setName($_POST['name']);
        $comment->setEmail($_POST['email']);
        $comment->setMessage($_POST['message']);
        $store->update($comment);
        return "<h1>Comment updated</h1><p><a href='/comments'>to list</a></p>";
    }
    
    #[Route('/comments/{id}/confirm-delete')]
    public function confirmDelete(string $id, EntityStorageInterface $store): string {
        $comment = $store->read(Comment::class, $id);
        return "
            <p>You are about to delete comment '".htmlspecialchars($comment->getName(), ENT_QUOTES)."', from '".$comment->getEmail()."'. This cannot be undone. Are you sure?</p>
            <a href=\"/comments\">Cancel</a>
            <form action='/comments/".htmlspecialchars($comment->getId(), ENT_QUOTES)."/delete' method='post'>
                <input type='hidden' name='id' value='".htmlspecialchars($id, ENT_QUOTES)."' />
                <button type='submit'>Delete</button>
                </form>
            </form>";
    }

    #[Route('/comments/{id}/delete')]
    public function delete(string $id, EntityStorageInterface $store): string {
        $store->delete($store->read(Comment::class, $id));
        return "<h1>Comment deleted</h1><p><a href='/comments'>to list</a></p>";
    }
}