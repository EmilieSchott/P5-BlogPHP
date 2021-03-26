<?php

namespace EmilieSchott\BlogPHP\Controller;

use EmilieSchott\BlogPHP\Model\UserManager;
use Twig\Environment;

class PrivateController
{
    public function connexionPage(Environment $twig, array $datas): void
    {
        echo $twig->render('connexionView.html.twig', $datas);
    }

    public function getConnexion(UserManager $userManager)
    {
        $attempt = [
            'email' => $_POST['email'],
            'password' => $_POST['password']
        ];

        try {
            $user = $userManager->getUser($attempt['email']);

            if (!empty($user) && \password_verify($attempt['password'], $user->getPassword())) {
                $_SESSION['id'] = $user->getId();
                $_SESSION['role'] = $user->getRole();
                $_SESSION['pseudo'] = $user->getPseudo();
                $_SESSION['name'] = $user->getName();
                $_SESSION['firstName'] = $user->getFirstName();
                $_SESSION['email'] = $user->getEmail();

                header('Location: index.php?action=account');
            } else {
                throw new \Exception("L'email ou le mot de passe est invalide.");
            }
        } catch (\Exception $e) {
            $_SESSION['connexionException'] = $e->getMessage();
            header('Location: index.php?action=connexion#exceptionMessage');
        }
    }

    public function accountPage(Environment $twig): void
    {
        echo $twig->render('accountView.html.twig');
    }
}
