<?php

namespace Framework\Controllers;

use Framework\Attributes\Route;
use Framework\Entity\User;
use Framework\Http\RedirectResponse;
use Framework\Http\Response;
use Framework\Http\ValidationErrorResponse;
use Framework\Storage\EntitySearchInterface;
use Twig\Environment;

class AuthenticationController
{
    #[Route('/login')]
    public function login(Environment $twig, EntitySearchInterface $store): Response
    {
        if (isset($_POST['email']) && isset($_POST['password'])) {
            /** @var ?User $user */
            $user = $store->find(User::class, ['email' => $_POST['email']])[0] ?? null;
            if (!$user || !$user->verifyPassword($_POST['password'])) {
                return new ValidationErrorResponse("Wrong username or password");
            }
            $_SESSION['user'] = $user;
            return new RedirectResponse('/');
        }
        return new Response($twig->render("users/login.html.twig"));
    }
}