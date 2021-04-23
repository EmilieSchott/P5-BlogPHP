<?php

session_start();

use EmilieSchott\BlogPHP\Controller\PrivateController;
use EmilieSchott\BlogPHP\Controller\PublicController;
use EmilieSchott\BlogPHP\Model\CommentManager;
use EmilieSchott\BlogPHP\Model\PostManager;
use EmilieSchott\BlogPHP\Model\UserManager;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

require_once __DIR__ . '/vendor/autoload.php';

$loader = new FilesystemLoader(__DIR__ . '/view');
$twig = new Environment($loader, [
    'cache' => false // "False" to replace by " __DIR__ . '/tmp' " to enable cache.
]);
$publicController = new PublicController();
$privateController = new PrivateController();
$postManager = new PostManager();
$commentManager = new CommentManager();
$userManager = new UserManager();

date_default_timezone_set('Etc/UTC');

if (isset($_SESSION['role'], $_SESSION['pseudo'])) {
    $datas['userSession'] = [
        'role' => $_SESSION['role'],
        'pseudo' => $_SESSION['pseudo']
    ];
}

try {
    if (isset($_GET['action'])) {
        $datas['action'] = htmlspecialchars($_GET['action']);
        switch ($datas['action']) {
            case 'homePage':
                if (isset($_SESSION['homePageException'])) {
                    $datas['homePageException'] = $_SESSION['homePageException'];
                    unset($_SESSION['homePageException']);
                }
                if (isset($_SESSION['contactFormMessage'])) {
                    $datas['contactFormMessage'] = $_SESSION['contactFormMessage'];
                    unset($_SESSION['contactFormMessage']);
                }
                if (isset($_GET['send'])) {
                    $datas['send'] = (int) $_GET['send'];
                }
                $publicController->homePage($twig, $datas);

                break;
            case 'contactMe':
                try {
                    $publicController->contactMe();
                    header('Location: index.php?action=homePage&send=1#contact-tool');
                } catch (\Exception $e) {
                    $_SESSION['contactFormMessage'] = $e->getMessage();
                    header('Location: index.php?action=homePage&send=0#contact-tool');
                }
                
                break;
            case 'blog':
                $datas['page'] = isset($_GET['page']) ? (int) $_GET['page'] : 1;
                if (isset($_SESSION['blogException'])) {
                    $datas['blogException'] = $_SESSION['blogException'];
                    unset($_SESSION['blogException']);
                }
                $publicController->blog($postManager, $twig, $datas);

                break;
            case 'post':
                try {
                    if (isset($_GET['id'])) {
                        $datas['id'] = (int) $_GET['id'];
                        $datas['page'] = isset($_GET['page']) ? (int) $_GET['page'] : 1;
                        $datas['blogPage'] = isset($_GET['blogPage']) ? (int) $_GET['blogPage'] : null;
                        $datas['success'] = isset($_GET['success']) ?  (int) $_GET['success'] : null;
                        if (isset($_SESSION['commentException'])) {
                            $datas['commentException'] = $_SESSION['commentException'];
                            unset($_SESSION['commentException']);
                        }
                        $publicController->post($postManager, $commentManager, $twig, $datas);
                    } else {
                        throw new Exception("aucun identifiant de billet n'a été indiqué.");
                    }
                } catch (\Exception $blogException) {
                    $_SESSION['blogException'] = $blogException;
                    header('Location: index.php?action=blog&page=1');
                }

                break;
            case 'addComment':
                $publicController->addComment($commentManager);

                break;
            case 'connexion':
                if (isset($_SESSION['pseudo'])) {
                    header('Location: index.php?action=account');
                }
                if (isset($_SESSION['connexionException'])) {
                    $datas['connexionException'] = $_SESSION['connexionException'];
                    unset($_SESSION['connexionException']);
                }
                $privateController->connexionPage($twig, $datas);

                break;
            case 'getConnexion':
                $privateController->getConnexion($userManager);

                break;
            case 'account':
                try {
                    if (isset($_SESSION['pseudo'])) {
                        $privateController->accountPage($twig, $userManager, $datas);
                    } else {
                        throw new \Exception("Vous ne pouvez accéder à cette page. Il faut vous identifier.");
                    }
                } catch (\Exception $e) {
                    $_SESSION['connexionException'] = $e->getMessage();
                    header('Location: index.php?action=connexion#exceptionMessage');
                }

                break;
            case 'inscription':
                if (isset($_SESSION['pseudo'])) {
                    header('Location: index.php?action=account');
                }
                if (isset($_SESSION['inscriptionException'])) {
                    $datas['inscriptionException'] = $_SESSION['inscriptionException'];
                    unset($_SESSION['inscriptionException']);
                }
                $datas['success'] = isset($_GET['success']) ?  (int) $_GET['success'] : null;
                $privateController->inscriptionPage($twig, $datas);

                break;
            case 'getInscription':
                $privateController->getInscription($userManager);

                break;
            case 'disconnect':
                try {
                    if (isset($_SESSION['pseudo'])) {
                        $privateController->disconnect();
                    } else {
                        throw new \Exception("Vous ne pouvez accéder à cette page. Il faut vous identifier.");
                    }
                } catch (\Exception $e) {
                    $_SESSION['connexionException'] = $e->getMessage();
                    header('Location: index.php?action=connexion#exceptionMessage');
                }

                break;
            case 'modifyDatas':
                try {
                    if (isset($_SESSION['pseudo'])) {
                        $datas['pseudo'] = ($_SESSION['role'] === 'Admin' && isset($_GET['pseudo'])) ?  (string) $_GET['pseudo'] : $_SESSION['pseudo'];
                        if (isset($_SESSION['modificationException'])) {
                            $datas['modificationException'] = $_SESSION['modificationException'];
                            unset($_SESSION['modificationException']);
                        }
                        $datas['success'] = isset($_GET['success']) ?  (int) $_GET['success'] : null;
                        $privateController->modifyDatas($userManager, $twig, $datas);
                    } else {
                        throw new \Exception("Vous ne pouvez accéder à cette page. Il faut vous identifier.");
                    }
                } catch (\Exception $e) {
                    $_SESSION['connexionException'] = $e->getMessage();
                    header('Location: index.php?action=connexion#exceptionMessage');
                }

                break;
            case 'modifyUser':
                try {
                    if (isset($_SESSION['pseudo'])) {
                        $privateController->modifyUser($userManager, $twig);
                    } else {
                        throw new \Exception("Vous ne pouvez accéder à cette page. Il faut vous identifier.");
                    }
                } catch (\Exception $e) {
                    $_SESSION['connexionException'] = $e->getMessage();
                    header('Location: index.php?action=connexion#exceptionMessage');
                }

                break;
            case 'myComments':
                try {
                    if (isset($_SESSION['pseudo'])) {
                        $datas['page'] = isset($_GET['page']) ? (int) $_GET['page'] : 1;
                        if (isset($_SESSION['commentException'])) {
                            $datas['commentException'] = $_SESSION['commentException'];
                            unset($_SESSION['commentException']);
                        }
                        $privateController->myComments($commentManager, $postManager, $twig, $datas);
                    } else {
                        throw new \Exception("Vous ne pouvez accéder à cette page. Il faut vous identifier.");
                    }
                } catch (\Exception $e) {
                    $_SESSION['connexionException'] = $e->getMessage();
                    header('Location: index.php?action=connexion#exceptionMessage');
                }

                break;
            case 'managePosts':
                if (isset($_SESSION['pseudo']) and $_SESSION['role'] === 'Admin') {
                    $datas['page'] = isset($_GET['page']) ? (int) $_GET['page'] : 1;
                    if (isset($_SESSION['exceptionMessage'])) {
                        $datas['exceptionMessage'] = $_SESSION['exceptionMessage'];
                        unset($_SESSION['exceptionMessage']);
                    }
                    $privateController->managePosts($postManager, $twig, $datas);
                } else {
                    throw new \Exception("Vous ne possédez pas les droits pour accéder à cette page.");
                }

                break;
            case 'confirmDeletion':
                    if (isset($_SESSION['pseudo']) and $_SESSION['role'] === 'Admin') {
                        $datas['entity'] = isset($_GET['entity']) ? (string) $_GET['entity'] : null;
                        $datas['id'] = isset($_GET['id']) ? (int) $_GET['id'] : null;
                        $datas['pseudo'] = isset($_GET['pseudo']) ? (string) $_GET['pseudo'] : null;
                        $privateController->confirmDeletion($postManager, $userManager, $twig, $datas);
                    } else {
                        throw new \Exception("Vous ne possédez pas les droits pour accéder à cette page.");
                    }
    
                break;
            case 'deleteEntity':
                    if (isset($_SESSION['pseudo']) and $_SESSION['role'] === 'Admin') {
                        $datas['entity'] = isset($_GET['entity']) ? (string) $_GET['entity'] : null;
                        $datas['id'] = isset($_GET['id']) ? (int) $_GET['id'] : null;
                        $datas['pseudo'] = isset($_GET['pseudo']) ? (string) $_GET['pseudo'] : null;
                        $privateController->deleteEntity($postManager, $userManager, $twig, $datas);
                    } else {
                        throw new \Exception("Vous ne possédez pas les droits pour accéder à cette page.");
                    }
    
                    break;
            case 'postFormPage':
                if (isset($_SESSION['pseudo']) and $_SESSION['role'] === 'Admin') {
                    $datas['postId'] = isset($_GET['post']) ? (int) $_GET['post'] : null;
                    $datas['success'] = isset($_GET['success']) ?  (int) $_GET['success'] : null;
                    if (isset($_SESSION['exceptionMessage'])) {
                        $datas['exceptionMessage'] = $_SESSION['exceptionMessage'];
                        unset($_SESSION['exceptionMessage']);
                    }
                    $privateController->postFormPage($postManager, $twig, $datas);
                } else {
                    throw new \Exception("Vous ne possédez pas les droits pour accéder à cette page.");
                }

                break;
            case 'sendPostForm':
                if (isset($_SESSION['pseudo']) and $_SESSION['role'] === 'Admin') {
                    $privateController->sendPostForm($userManager, $postManager, $datas);
                } else {
                    throw new \Exception("Vous ne possédez pas les droits pour accéder à cette page.");
                }

                break;
            case 'manageComments':
                if (isset($_SESSION['pseudo']) and $_SESSION['role'] === 'Admin') {
                    $datas['page'] = isset($_GET['page']) ? (int) $_GET['page'] : 1;
                    $datas['success'] = isset($_GET['success']) ?  (int) $_GET['success'] : null;
                    if (isset($_SESSION['exceptionMessage'])) {
                        $datas['exceptionMessage'] = $_SESSION['exceptionMessage'];
                        unset($_SESSION['exceptionMessage']);
                    }
                    $privateController->manageComments($commentManager, $postManager, $twig, $datas);
                } else {
                    throw new \Exception("Vous ne possédez pas les droits pour accéder à cette page.");
                }

                break;
                case 'modifyCommentStatus':
                    if (isset($_SESSION['pseudo']) and $_SESSION['role'] === 'Admin') {
                        $datas['id'] = isset($_GET['id']) ? (int) $_GET['id'] : null;
                        $datas['status'] = isset($_GET['status']) ? (string) $_GET['status'] : null;
                        $privateController->modifyCommentStatus($commentManager, $datas);
                    } else {
                        throw new \Exception("Vous ne possédez pas les droits pour accéder à cette page.");
                    }
    
                    break;
                case 'manageUsers':
                    if (isset($_SESSION['pseudo']) and $_SESSION['role'] === 'Admin') {
                        $datas['page'] = isset($_GET['page']) ? (int) $_GET['page'] : 1;
                        $datas['success'] = isset($_GET['success']) ?  (int) $_GET['success'] : null;
                        if (isset($_SESSION['exceptionMessage'])) {
                            $datas['exceptionMessage'] = $_SESSION['exceptionMessage'];
                            unset($_SESSION['exceptionMessage']);
                        }
                        $privateController->manageUsers($userManager, $twig, $datas);
                    } else {
                        throw new \Exception("Vous ne possédez pas les droits pour accéder à cette page.");
                    }
    
                    break;
            default:
                throw new Exception("L'action indiquée n'est pas valide.");

                break;
        }
    } else {
        header('Location: index.php?action=homePage');
    }
} catch (\Exception $e) {
    $_SESSION['homePageException'] = $e->getMessage();
    header('Location: index.php?action=homePage');
}
