<?php

namespace EmilieSchott\BlogPHP\Controller;

use EmilieSchott\BlogPHP\Model\CommentManager;
use EmilieSchott\BlogPHP\Model\PostManager;
use EmilieSchott\BlogPHP\Model\User;
use EmilieSchott\BlogPHP\Model\UserManager;
use EmilieSchott\BlogPHP\Paginator\Paginator;
use Twig\Environment;

class PrivateController
{
    use Paginator;

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

            if ($user instanceof User && \password_verify($attempt['password'], $user->getPassword())) {
                $_SESSION['role'] = $user->getRole();
                $_SESSION['pseudo'] = $user->getPseudo();

                header('Location: index.php?action=account');
            } else {
                throw new \Exception("Le pseudo ou le mot de passe est invalide.");
            }
        } catch (\Exception $e) {
            $_SESSION['connexionException'] = $e->getMessage();
            header('Location: index.php?action=connexion#exceptionMessage');
        }
    }

    public function accountPage(Environment $twig, UserManager $userManager, array $datas): void
    {
        $datas['office'] = 'back';
        $datas['user'] = $userManager->getUser($_SESSION['pseudo']);
        echo $twig->render('accountView.html.twig', $datas);
    }

    public function inscriptionPage(Environment $twig, array $datas): void
    {
        if (array_key_exists('success', $datas) && ($datas['success'] < 0 || $datas['success'] > 1)) {
            unset($datas['success']);
        }

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
            $verifyPseudo = $userManager->getUser($datas['pseudo']);
            if ($verifyPseudo instanceof User) {
                throw new \Exception("Ce pseudo est déjà pris, choisissez-en un autre.");
            } else {
                $userManager->addUser($datas);
                header('Location: index.php?action=inscription&success=1');
            }
        } catch (\Exception $e) {
            $_SESSION['inscriptionException'] = $e->getMessage();
            header('Location: index.php?action=inscription&success=0#exceptionMessage');
        }
    }

    public function disconnect()
    {
        \session_destroy();
        header('Location: index.php?action=homePage');
    }
    
    public function modifyMyDatas(UserManager $userManager, Environment $twig, array $datas)
    {
        $datas['office'] = 'back';
        $datas['user'] = $userManager->getUser($_SESSION['pseudo']);
        if (array_key_exists('success', $datas) && ($datas['success'] < 0 || $datas['success'] > 1)) {
            unset($datas['success']);
        }

        echo $twig->render('datasModificationView.html.twig', $datas);
    }

    public function modifyUser(UserManager $userManager, Environment $twig)
    {
        try {
            $datas['office'] = 'back';
            $user = $userManager->getUser($_SESSION['pseudo']);

            if ($_POST['formName'] === 'modifyEmail') {
                if (isset($_POST['email'])) {
                    $modification = [
                        'email' => $_POST['email']
                        ];
                } else {
                    throw new \Exception("Le champ n'est pas rempli.");
                }
            }

            if ($_POST['formName'] === 'modifyPassword') {
                if (isset($_POST['password0'], $_POST['password1'], $_POST['password2'])) {
                    if (\password_verify($_POST['password0'], $user->getPassword())) {
                        if ($_POST['password1'] === $_POST['password2']) {
                            $modification = [
                                'password' => \password_hash($_POST['password1'], PASSWORD_DEFAULT)
                            ];
                        } else {
                            throw new \Exception("Les nouveaux mots de passe ne sont pas identiques.");
                        }
                    } else {
                        throw new \Exception("L'ancien mot de passe n'est pas correct.");
                    }
                } else {
                    throw new \Exception("Les champs ne sont pas remplis.");
                }
            }

            $datas['user'] = $userManager->modifyUser($user, $modification);
            header('Location: index.php?action=modifyMyDatas&success=1#message');
        } catch (\Exception $e) {
            $_SESSION['modificationException'] = $e->getMessage();
            header('Location: index.php?action=modifyMyDatas&success=0#message');
        }
    }

    public function myComments(CommentManager $commentManager, PostManager $postManager, Environment $twig, array $datas)
    {
        $datas['office'] = 'back';
        
        try {
            $comments = $commentManager->getUserCommments($_SESSION['pseudo']);
        } catch (\Exception $e) {
            $datas['commentsException'] = "Le(s) commentaire(s) n'a/ont pas pu être récupéré(s)";
        }

        $commentsPages = $this->paginator($comments, 5);
        if (!is_null($commentsPages['pagesNbr'])) {
            $datas['pagesNbr'] = $commentsPages['pagesNbr'];

            try {
                if ($datas['page'] <= 0 || $datas['page'] > $datas['pagesNbr']) {
                    throw new \Exception("La page de commentaires indiquée n'existe pas.");
                }
            } catch (\Exception $e) {
                $datas['invalidCommentsPage'] = $e->getMessage();
                $datas['page'] = 1;
            }

            $commentsPage = $this->displayPage($commentsPages['datasPages'], $datas['page']);
        } elseif (array_key_exists('page', $datas)) {
            unset($datas['page']);
        }

        foreach ($commentsPage as $comment) {
            $post = $postManager->getPost($comment->getPostId());
            $postTitle = $post->getTitle();
            $datas['comments'][] = [
                'datas' => $comment,
                'postTitle' => $postTitle
            ];
        }

        echo $twig->render('myCommentsView.html.twig', $datas);
    }

    public function managePosts(PostManager $postManager, Environment $twig, array $datas)
    {
        $datas['office'] = 'back';
        
        try {
            $posts = $postManager->getList();
        } catch (\Exception $e) {
            $datas['postException'] ="Le(s) post(s) n'a/ont pas pu être récupéré(s)";
        }
        $postsPages = $this->paginator($posts, 5);
        $datas['posts'] = $postsPages['datasPages'];
        $datas['pagesNbr'] = $postsPages['pagesNbr'];

        try {
            if ($datas['page'] <= 0 || $datas['page'] > $datas['pagesNbr']) {
                $datas['page'] = 1;

                throw new \Exception("La page indiquée n'existe pas.");
            }
        } catch (\Exception $e) {
            $datas['invalidPostPage'] = $e->getMessage();
        }

        $datas['posts'] =  $this->displayPage($datas['posts'], $datas['page']);
        echo $twig->render('adminManagePostsView.html.twig', $datas);
    }
}
