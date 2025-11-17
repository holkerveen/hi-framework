<?php
// src/Controllers/CommentController.php

namespace Framework\Controllers;

use Framework\Attributes\Route;
use Framework\Entity\Comment;
use Framework\Storage\EntityStorageInterface;

class CommentController
{
    #[Route('/comments')]
    public function index(EntityStorageInterface $store): string
    {
        $comments = $store->index(Comment::class);
        $count = count($store->index(Comment::class));
        $list = "<ul>" . implode("", array_map(function ($c) {
                return "<li>"
                    . "id:" . htmlspecialchars($c->getId(), ENT_QUOTES) . "<br>"
                    . "name:" . htmlspecialchars($c->getName(), ENT_QUOTES) . "<br>"
                    . "email:" . htmlspecialchars($c->getEmail(), ENT_QUOTES) . "<br>"
                    . "message:" . nl2br(htmlspecialchars($c->getMessage(), ENT_QUOTES)) . "<br>"
                    . "</li>";
            }, $comments)) . "</ul>";

        return "
            <h1>Comments</h1>
            <p>Comments found: $count</p>
            <p><a href='/comments/create'>Create new</a></p>
            $list";
    }
    
    #[Route('/comments/create')]
    public function create(): string
    {
        return "<form action='/comments/post' method='post'>
            <label style='display:block;'>Name<input name='name'/></label>
            <label style='display:block;'>Email<input name='email'/></label>
            <label style='display:block;'>Message<textarea name='message'/></textarea></label>
            <button>Create</button>
            </form>";
    }
    
    #[Route('/comments/post')]
    public function post(EntityStorageInterface $store): string {
        $comment = new Comment();
        $comment->setName($_POST['name']);
        $comment->setEmail($_POST['email']);
        $comment->setMessage($_POST['message']);
        $store->create($comment);
        return "<h1>Comment created</h1><p><a href='/comments'>to list</a></p>";
    }
}