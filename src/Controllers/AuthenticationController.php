<?php

namespace Hi\Controllers;

use Hi\Attributes\AllowAccess;
use Hi\Attributes\Route;
use Hi\Entity\User;
use Hi\Enums\Role;
use Hi\Http\RedirectResponse;
use Hi\Http\Response;
use Hi\Http\ValidationErrorResponse;
use Hi\SessionInterface;
use Hi\SessionUser;
use Hi\Storage\EntitySearchInterface;
use Twig\Environment;

class AuthenticationController
{
    #[Route('/login')]
    #[AllowAccess(Role::Unauthenticated)]
    public function login(Environment $twig, EntitySearchInterface $store, SessionInterface $session): Response
    {
        if (isset($_POST['email']) && isset($_POST['password'])) {
            /** @var ?User $user */
            $user = $store->find(User::class, ['email' => $_POST['email']])[0] ?? null;
            if (!$user || !$user->verifyPassword($_POST['password'])) {
                return new ValidationErrorResponse("Wrong username or password");
            }
            $session->set('user', SessionUser::fromUser($user));
            return new RedirectResponse('/');
        }
        return new Response($twig->render("users/login.html.twig"));
    }

    #[Route('/logout')]
    #[AllowAccess(Role::Unauthenticated)]
    public function logout(Environment $twig, SessionInterface $session): Response
    {
        $session->clear();
        return new Response($twig->render("users/logout.html.twig"));
    }
}