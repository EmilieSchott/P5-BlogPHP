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

    public function getConnexion(): void
    {
        $attempt = [
            'pseudo' => $_POST['pseudo'],
            'password' => $_POST['password']
        ];

        try {
            if (filter_var($attempt['pseudo'], FILTER_VALIDATE_EMAIL)) {
                throw new \Exception("Vous avez entré un email. C'est votre pseudo qui est demandé.");
            }
            $userManager = new UserManager();
            $user = $userManager->getUser($attempt['pseudo']);

            if ($user instanceof User && \password_verify($attempt['password'], $user->getPassword())) {
                $_SESSION['role'] = $user->getRole();
                $_SESSION['pseudo'] = $user->getPseudo();
                $_SESSION['token'] = md5(bin2hex(\openssl_random_pseudo_bytes(6)));

                header('Location: index.php?action=account');
            } else {
                throw new \Exception("Le pseudo ou le mot de passe est invalide.");
            }
        } catch (\Exception $e) {
            $_SESSION['connexionException'] = $e->getMessage();
            header('Location: index.php?action=connexion#exceptionMessage');
        }
    }

    public function accountPage(Environment $twig, array $datas): void
    {
        $datas['office'] = 'back';
        $userManager = new UserManager();
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

    public function getInscription(): void
    {
        $datas = [
            'role' => 'Lecteur',
            'pseudo' => $_POST['pseudo'],
            'name' => $_POST['name'],
            'firstName' => $_POST['firstName'],
            'email' => $_POST['email'],
            'password' => \password_hash($_POST['password'], PASSWORD_DEFAULT)
        ];

        try {
            $userManager = new UserManager();
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

    public function disconnect(): void
    {
        \session_destroy();
        header('Location: index.php?action=homePage');
    }
    
    public function modifyDatas(Environment $twig, array $datas): void
    {
        $datas['office'] = 'back';
        if (array_key_exists('success', $datas) && ($datas['success'] < 0 || $datas['success'] > 1)) {
            unset($datas['success']);
        }
        $userManager = new UserManager;
        $datas['user'] = $userManager->getUser($datas['pseudo']);
        unset($datas['pseudo']);
        echo $twig->render('datasModificationView.html.twig', $datas);
    }

    public function modifyUser(): void
    {
        try {
            $datas['office'] = 'back';
            $userManager = new UserManager();
            $user = $userManager->getUser($_POST['pseudo']);

            if (method_exists($this, $_POST['formName'])) {
                $method = $_POST['formName'];
                $modification = $this->{$method}($user);
            }

            $datas['user'] = $userManager->modifyUser($user, $modification);
            header('Location: index.php?action=modifyDatas&success=1#message');
        } catch (\Exception $e) {
            $_SESSION['exceptionMessage'] = $e->getMessage();
            header('Location: index.php?action=modifyDatas&success=0#message');
        }
    }

    public function modifyEmail(object $user): array
    {
        if (isset($_POST['email'])) {
            if ($user->getEmail() !== $_POST['email']) {
                $modification = [
                    'email' => $_POST['email']
                ];
            
                return $modification;
            } else {
                throw new \Exception("L'ancien et le nouvel email sont identiques.");
            }
        } else {
            throw new \Exception("Le champ n'est pas rempli.");
        }
    }

    public function modifyPassword(object $user): array
    {
        if (isset($_POST['password0'], $_POST['password1'], $_POST['password2'])) {
            if (\password_verify($_POST['password0'], $user->getPassword())) {
                if ($_POST['password1'] === $_POST['password2']) {
                    $modification = [
                        'password' => \password_hash($_POST['password1'], PASSWORD_DEFAULT)
                    ];

                    return $modification;
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

    public function myComments(Environment $twig, array $datas): void
    {
        $datas['office'] = 'back';
        
        try {
            $commentManager = new CommentManager();
            $comments = $commentManager->getUserCommments($_SESSION['pseudo']);
            $commentsPages = $this->paginator($comments, 5);
            $datas['pagesNumber'] = $commentsPages['pagesNumber'];
            $commentsPage = $this->displayPage($commentsPages, $datas['page']);
            $datas['comments'] = $this->getCommentPostTitle($commentsPage);
            echo $twig->render('myCommentsView.html.twig', $datas);
        } catch (\Exception $e) {
            $datas['commentsException'] = $e->getMessage();
            echo $twig->render('myCommentsView.html.twig', $datas);
        }
    }

    public function getCommentPostTitle(array $commentsPage): array
    {
        foreach ($commentsPage as $comment) {
            $postManager = new PostManager();
            $post = $postManager->getPost($comment->getPostId());
            $postTitle = $post->getTitle();
            $datas['comments'][] = [
                'datas' => $comment,
                'postTitle' => $postTitle
            ];
        }

        return $datas['comments'];
    }

    public function managePosts(Environment $twig, array $datas): void
    {
        $datas['office'] = 'back';

        try {
            $postManager = new PostManager();
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

    public function confirmDeletion(Environment $twig, array $datas): void
    {
        $datas['office'] = 'back';
        
        try {
            if ($datas['entity'] === 'post') {
                $this->confirmPostSuppression($twig, $datas);
            } elseif ($datas['entity'] === 'user') {
                $this->confirmUserSuppression($twig, $datas);
            } else {
                throw new \Exception("L'action entreprise n'est pas valide.");
            }
        } catch (\Exception $e) {
            $datas['exceptionMessage']=$e->getMessage();
            echo $twig->render('adminConfirmDeleteView.html.twig', $datas);
        }
    }

    public function confirmPostSuppression(Environment $twig, array $datas): void
    {
        if (!empty($datas['id'])) {
            $postManager = new PostManager();
            $datas['post'] = $postManager->getPost($datas['id']);
            if ($datas['post'] instanceof Post) {
                echo $twig->render('adminConfirmDeleteView.html.twig', $datas);
            } else {
                throw new \Exception("Le billet n'a pas pu être récupéré");
            }
        } else {
            throw new \Exception("Aucun billet valide n'a été spécifié.");
        }
    }

    public function confirmUserSuppression(Environment $twig, array $datas): void
    {
        if (!empty($datas['pseudo'])) {
            $userManager = new UserManager();
            $datas['user'] = $userManager->getUser($datas['pseudo']);
            if ($datas['user'] instanceof User) {
                echo $twig->render('adminConfirmDeleteView.html.twig', $datas);
            } else {
                throw new \Exception("L'utilisateur n'a pas pu être récupéré");
            }
        } else {
            throw new \Exception("Aucun utilisateur valide n'a été spécifié.");
        }
    }

    public function deleteEntity(Environment $twig, array $datas): void
    {
        $datas['office'] = 'back';
        
        try {
            if ($datas['entity'] === 'post') {
                $this->deletePost($twig, $datas);
            } elseif ($datas['entity'] === 'user') {
                $this->deleteUser($twig, $datas);
            } else {
                throw new \Exception("L'action entreprise n'est pas valide.");
            }
        } catch (\Exception $e) {
            $datas['exceptionMessage']=$e->getMessage();
            echo $twig->render('adminConfirmDeleteView.html.twig', $datas);
        }
    }

    public function deletePost(Environment $twig, array $datas): void
    {
        if (!empty($datas['id'])) {
            $postManager = new PostManager();
            $post = $postManager->getPost($datas['id']);
            \unlink('public/upload/img/post/' . $post->getPicture());
            $postManager->deletePost($datas['id']);
            $datas['deleteSuccess']=1;
            echo $twig->render('adminConfirmDeleteView.html.twig', $datas);
        } else {
            throw new \Exception("Aucun post n'a été spécifié.");
        }
    }

    public function deleteUser(Environment $twig, array $datas): void
    {
        if (!empty($datas['pseudo'])) {
            $userManager = new UserManager();
            $userManager->deleteUser($datas['pseudo']);
            $datas['deleteSuccess']=1;
            echo $twig->render('adminConfirmDeleteView.html.twig', $datas);
        } else {
            throw new \Exception("Aucun utilisateur n'a été spécifié.");
        }
    }

    public function postFormPage(Environment $twig, array $datas): void
    {
        try {
            $datas['office'] = 'back';
            $postManager = new PostManager();
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

    public function sendPostForm(array $datas): void
    {
        $datas = [
            'title' => $_POST['title'],
            'author' => $_POST['author'],
            'pictureDescription' => $_POST['pictureDescription'],
            'standfirst' => $_POST['standfirst'],
            'content' => $_POST['content']
        ];
        
        try {
            $userManager = new UserManager();
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

            $postManager = new PostManager();
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

    public function validateImageFile(): string
    {
        if ($_FILES['newPicture']['error'] === 0) {
            if ($_FILES['newPicture']['size'] <= 2000000) {
                $authorizedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
                $dataFile = \pathinfo($_FILES['newPicture']['name']);
                $fileExtension = \strtolower($dataFile['extension']);

                if (in_array($fileExtension, $authorizedExtensions, true)) {
                    if (\strlen($_FILES['newPicture']['name']) >= 150) {
                        $cutName = str_split($dataFile['filename'], 140);
                        $fileName = $cutName[0];
                    } else {
                        $fileName = $dataFile['filename'];
                    }

                    $newFileName = bin2hex(\openssl_random_pseudo_bytes(6));
                    $newFileName .= $fileName;
                    $newFileName .= '.' . $fileExtension;
                    \move_uploaded_file($_FILES['newPicture']['tmp_name'], 'public/upload/img/post/' . $newFileName);

                    return $newFileName;
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
    
    public function manageComments(Environment $twig, array $datas): void
    {
        $datas['office'] = 'back';

        try {
            if (array_key_exists('success', $datas) && ($datas['success'] < 0 || $datas['success'] > 1)) {
                unset($datas['success']);
            }
            $commentManager = new CommentManager();
            $comments = $commentManager->getList();
            $commentsPages = $this->paginator($comments, 5);
            $datas['pagesNumber'] = $commentsPages['pagesNumber'];
            $commentsPage =  $this->displayPage($commentsPages, $datas['page']);
            $datas['comments'] = $this->getCommentPostTitle($commentsPage);
            echo $twig->render('adminManageView.html.twig', $datas);
        } catch (\Exception $e) {
            $datas['exceptionMessage'] = $e->getMessage();
            echo $twig->render('adminManageView.html.twig', $datas);
        }
    }

    public function modifyCommentStatus(array $datas): void
    {
        $datas['office'] = 'back';
        
        try {
            $commentManager = new CommentManager();
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

    public function manageUsers(Environment $twig, array $datas): void
    {
        $datas['office'] = 'back';

        try {
            if (array_key_exists('success', $datas) && ($datas['success'] < 0 || $datas['success'] > 1)) {
                unset($datas['success']);
            }
            $userManager = new UserManager();
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
