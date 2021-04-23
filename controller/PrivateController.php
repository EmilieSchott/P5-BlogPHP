<?php

namespace EmilieSchott\BlogPHP\Controller;

use EmilieSchott\BlogPHP\Model\CommentManager;
use EmilieSchott\BlogPHP\Model\Post;
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
            $commentsPages = $this->paginator($comments, 5);
            $datas['pagesNumber'] = $commentsPages['pagesNumber'];
            $commentsPage = $this->displayPage($commentsPages, $datas['page']);
            $datas['comments'] = $this->getCommentPostTitle($commentsPage, $postManager);
            echo $twig->render('myCommentsView.html.twig', $datas);
        } catch (\Exception $e) {
            $datas['commentsException'] = $e->getMessage();
            echo $twig->render('myCommentsView.html.twig', $datas);
        }
    }

    public function getCommentPostTitle(array $commentsPage, PostManager $postManager): array
    {
        foreach ($commentsPage as $comment) {
            $post = $postManager->getPost($comment->getPostId());
            $postTitle = $post->getTitle();
            $datas['comments'][] = [
                'datas' => $comment,
                'postTitle' => $postTitle
            ];
        }

        return $datas['comments'];
    }

    public function managePosts(PostManager $postManager, Environment $twig, array $datas)
    {
        $datas['office'] = 'back';

        try {
            $posts = $postManager->getList();
            $postsPages = $this->paginator($posts, 5);
            $datas['pagesNumber'] = $postsPages['pagesNumber'];
            $datas['posts'] =  $this->displayPage($postsPages, $datas['page']);
            echo $twig->render('adminManageView.html.twig', $datas);
        } catch (\Exception $e) {
            $datas['exceptionMessage'] = $e->getMessage();
            echo $twig->render('adminManageView.html.twig', $datas);
        }
    }

    public function deletePostPage($postManager, Environment $twig, array $datas)
    {
        $datas['office'] = 'back';
        
        try {
            if (!empty($datas['id'])) {
                $datas['post'] = $postManager->getPost($datas['id']);
                if ($datas['post'] instanceof Post) {
                    echo $twig->render('adminConfirmDeleteView.html.twig', $datas);
                } else {
                    throw new \Exception("Le billet n'a pas pu être récupéré");
                }
            } else {
                throw new \Exception("Aucun billet valide n'a été spécifié.");
            }
        } catch (\Exception $e) {
            $datas['postException']=$e->getMessage();
            echo $twig->render('adminConfirmDeleteView.html.twig', $datas);
        }
    }

    public function deletePost(PostManager $postManager, Environment $twig, array $datas)
    {
        $datas['office'] = 'back';
    
        try {
            if (!empty($datas['postId'])) {
                $post = $postManager->getPost($datas['postId']);
                \unlink('public/upload/img/post/' . $post->getPicture());
                $postManager->deletePost($datas['postId']);
                $datas['deleteSuccess']=1;
                echo $twig->render('adminConfirmDeleteView.html.twig', $datas);
            } else {
                throw new \Exception("Aucun post n'a été spécifié.");
            }
        } catch (\Exception $e) {
            $datas['postException']=$e->getMessage();
            echo $twig->render('adminConfirmDeleteView.html.twig', $datas);
        }
    }

    public function postFormPage(PostManager $postManager, Environment $twig, array $datas)
    {
        try {
            $datas['office'] = 'back';
            $datas['post'] = !empty($datas['postId']) ? $postManager->getPost($datas['postId']) : null;

            if (array_key_exists('success', $datas) && ($datas['success'] < 0 || $datas['success'] > 1)) {
                unset($datas['success']);
            }

            echo $twig->render('adminPostFormView.html.twig', $datas);
        } catch (\Exception $e) {
            $datas['exceptionMessage'] = $e->getMessage();
            echo $twig->render('adminManageView.html.twig', $datas);
        }
    }

    public function sendPostForm(UserManager $userManager, PostManager $postManager, array $datas)
    {
        $datas = [
            'title' => $_POST['title'],
            'author' => $_POST['author'],
            'pictureDescription' => $_POST['pictureDescription'],
            'standfirst' => $_POST['standfirst'],
            'content' => $_POST['content']
        ];
        
        try {
            $user = $userManager->getUser($_SESSION['pseudo']);
            $datas['userId'] = $user->getId();
            if (!empty($_FILES['newPicture']['name'])) {
                $datas['picture'] = $this->validateImageFile();
                if (!empty($_POST['oldPicture'])) {
                    \unlink('public/upload/img/post/' . $_POST['oldPicture']);
                }
            } else {
                $datas['picture'] = $_POST['oldPicture'];
            }

            if (isset($_POST['postId'])) {
                $datas['id'] = $_POST['postId'];
                $postManager->modifyPost($datas);
            } else {
                $postManager->addPost($datas);
            }
        
            header('Location: index.php?action=postFormPage&success=1#form');
        } catch (\Exception $e) {
            $_SESSION['exceptionMessage'] = $e->getMessage();
            header('Location: index.php?action=postFormPage&success=0#form');
        }
    }

    public function validateImageFile() : string
    {
        if ($_FILES['newPicture']['error'] === 0) {
            if ($_FILES['newPicture']['size'] <= 2000000) {
                $authorizedExtensions = ['jpg', 'JPG', 'jpeg', 'JPEG', 'png', 'PNG', 'gif', 'GIF'];
                $dataFile = \pathinfo($_FILES['newPicture']['name']);
                $fileExtension = $dataFile['extension'];
                if (in_array($fileExtension, $authorizedExtensions, true)) {
                    $fileName = $dataFile['filename'];
                    for ($i=1 ; file_exists('public/upload/img/post/' . $_FILES['newPicture']['name']) ; $i++) {
                        $_FILES['newPicture']['name'] = $fileName . "--" . $i . "." . $fileExtension;
                    }
                    \move_uploaded_file($_FILES['newPicture']['tmp_name'], 'public/upload/img/post/' . $_FILES['newPicture']['name']);

                    return $_FILES['newPicture']['name'];
                } else {
                    throw new \Exception("Le fichier image doit être au format .jpg, .png ou .gif.");
                }
            } else {
                throw new \Exception("Le fichier image doit faire au maximum 2Mo.");
            }
        } else {
            throw new \Exception("Le fichier image n'a pas pu être uploadé.");
        }
    }

    public function manageComments(CommentManager $commentManager, PostManager $postManager, Environment $twig, array $datas)
    {
        $datas['office'] = 'back';

        try {
            if (array_key_exists('success', $datas) && ($datas['success'] < 0 || $datas['success'] > 1)) {
                unset($datas['success']);
            }
            $comments = $commentManager->getList();
            $commentsPages = $this->paginator($comments, 5);
            $datas['pagesNumber'] = $commentsPages['pagesNumber'];
            $commentsPage =  $this->displayPage($commentsPages, $datas['page']);
            $datas['comments'] = $this->getCommentPostTitle($commentsPage, $postManager);
            echo $twig->render('adminManageView.html.twig', $datas);
        } catch (\Exception $e) {
            $datas['exceptionMessage'] = $e->getMessage();
            echo $twig->render('adminManageView.html.twig', $datas);
        }
    }

    public function modifyCommentStatus(CommentManager $commentManager, array $datas)
    {
        $datas['office'] = 'back';
        
        try {
            if (!empty($datas['id']) && $datas['id'] > 0) {
                $comment = $commentManager->getComment($datas['id']);
                $datasToModify['id'] = $datas['id'];
                switch ($datas['status']) {
                    case 'rejected':
                        $datasToModify['status'] = "Rejeté";

                        break;
                    case 'validated':
                        $datasToModify['status'] = "Validé";

                        break;
                    default:
                        throw new \Exception("Le nouveau statut n'est pas valide.");

                        break;
                }
            } else {
                throw new \Exception("Le commentaire indiqué n'est pas valide.");
            }

            $commentManager->modifyComment($comment, $datasToModify);
            header('Location: index.php?action=manageComments&success=1#message');
        } catch (\Exception $e) {
            $_SESSION['exceptionMessage'] = $e->getMessage();
            header('Location: index.php?action=manageComments&success=0#message');
        }
    }

    public function manageUsers(UserManager $userManager, Environment $twig, array $datas)
    {
        $datas['office'] = 'back';

        try {
            if (array_key_exists('success', $datas) && ($datas['success'] < 0 || $datas['success'] > 1)) {
                unset($datas['success']);
            }
            $users = $userManager->getList();
            $usersPages = $this->paginator($users, 10);
            $datas['pagesNumber'] = $usersPages['pagesNumber'];
            $datas['users'] =  $this->displayPage($usersPages, $datas['page']);
            echo $twig->render('adminManageView.html.twig', $datas);
        } catch (\Exception $e) {
            $datas['exceptionMessage'] = $e->getMessage();
            echo $twig->render('adminManageView.html.twig', $datas);
        }
    }
}
