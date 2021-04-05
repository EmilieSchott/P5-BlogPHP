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
            'pseudo' => $_POST['pseudo'],
            'password' => $_POST['password']
        ];

        try {
            if (filter_var($attempt['pseudo'], FILTER_VALIDATE_EMAIL)) {
                throw new \Exception("Vous avez entré un email. C'est votre pseudo qui est demandé.");
            }
            
            $user = $userManager->getUser($attempt['pseudo']);

            if (!empty($user) && \password_verify($attempt['password'], $user->getPassword())) {
                $_SESSION['id'] = $user->getId();
                $_SESSION['role'] = $user->getRole();
                $_SESSION['pseudo'] = $user->getPseudo();
                $_SESSION['name'] = $user->getName();
                $_SESSION['firstName'] = $user->getFirstName();
                $_SESSION['email'] = $user->getEmail();

                header('Location: index.php?action=account');
            } else {
                throw new \Exception("Le pseudo ou le mot de passe est invalide.");
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

    public function inscriptionPage(Environment $twig, array $datas): void
    {
        echo $twig->render('inscriptionView.html.twig', $datas);
    }

    public function getInscription(UserManager $userManager)
    {
        $datas = [
            'role' => 'Reader',
            'pseudo' => $_POST['pseudo'],
            'name' => $_POST['name'],
            'firstName' => $_POST['firstName'],
            'email' => $_POST['email'],
            'password' => \password_hash($_POST['password'], PASSWORD_DEFAULT)
        ];

        try {
            $userManager->addUser($datas);
        } catch (\Exception $e) {
            $_SESSION['inscriptionException'] = $e->getMessage();
            header('Location: index.php?action=inscription&success=0#exceptionMessage');
        }
        header('Location: index.php?action=inscription&success=1');
    }
}
