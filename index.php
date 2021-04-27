<?php

session_start();

use EmilieSchott\BlogPHP\Controller\PrivateController;
use EmilieSchott\BlogPHP\Controller\PublicController;

require_once __DIR__ . '/vendor/autoload.php';

date_default_timezone_set('Etc/UTC');

if (isset($_SESSION['role'], $_SESSION['pseudo'])) {
    $datas['userSession'] = [
        'role' => $_SESSION['role'],
        'pseudo' => $_SESSION['pseudo'],
        'token' => $_SESSION['token']
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
                $publicController = new PublicController();
                $publicController->homePage($datas);

                break;
            case 'contactMe':
                try {
                    $publicController = new PublicController();
                    $publicController->contactMe();
                    header('Location: index.php?action=homePage&send=1#contact-tool');
                } catch (\Exception $e) {
                    $_SESSION['contactFormMessage'] = $e->getMessage();
                    header('Location: index.php?action=homePage&send=0#contact-tool');
                }
                
                break;
            case 'blogIndex':
                $datas['page'] = isset($_GET['page']) ? (int) $_GET['page'] : 1;
                if (isset($_SESSION['blogException'])) {
                    $datas['blogException'] = $_SESSION['blogException'];
                    unset($_SESSION['blogException']);
                }
                $publicController = new PublicController();
                $publicController->blogIndex($datas);

                break;
            case 'postPage':
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
                        $publicController = new PublicController();
                        $publicController->postPage($datas);
                    } else {
                        throw new Exception("aucun identifiant de billet n'a été indiqué.");
                    }
                } catch (\Exception $blogException) {
                    $_SESSION['blogException'] = $blogException;
                    header('Location: index.php?action=blogIndex&page=1');
                }

                break;
            case 'addComment':
                $publicController = new PublicController();
                $publicController->addComment();

                break;
            case 'connexion':
                if (isset($_SESSION['pseudo'])) {
                    header('Location: index.php?action=accountPage');
                }
                if (isset($_SESSION['connexionException'])) {
                    $datas['connexionException'] = $_SESSION['connexionException'];
                    unset($_SESSION['connexionException']);
                }
                $privateController = new PrivateController();
                $privateController->connexionPage($datas);

                break;
            case 'getConnexion':
                $privateController = new PrivateController();
                $privateController->getConnexion();

                break;
            case 'accountPage':
                try {
                    if (isset($_SESSION['pseudo'])) {
                        $privateController = new PrivateController();
                        $privateController->accountPage($datas);
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
                    header('Location: index.php?action=accountPage');
                }
                if (isset($_SESSION['inscriptionException'])) {
                    $datas['inscriptionException'] = $_SESSION['inscriptionException'];
                    unset($_SESSION['inscriptionException']);
                }
                $datas['success'] = isset($_GET['success']) ?  (int) $_GET['success'] : null;
                $privateController = new PrivateController();
                $privateController->inscriptionPage($datas);

                break;
            case 'getInscription':
                $privateController = new PrivateController();
                $privateController->getInscription();

                break;
            case 'disconnect':
                try {
                    if (isset($_SESSION['pseudo'])) {
                        $privateController = new PrivateController();
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
                        if (isset($_SESSION['exceptionMessage'])) {
                            $datas['exceptionMessage'] = $_SESSION['exceptionMessage'];
                            unset($_SESSION['exceptionMessage']);
                        }
                        $datas['success'] = isset($_GET['success']) ?  (int) $_GET['success'] : null;
                        $privateController = new PrivateController();
                        $privateController->modifyDatas($datas);
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
                        if (isset($_POST['token']) && $_POST['token'] === $_SESSION['token']) {
                            $privateController = new PrivateController();
                            $privateController->modifyUser();
                        } else {
                            throw new \Exception("Vous ne pouvez effectuer cette action. Le jeton de session ne correspond pas.");
                        }
                    } else {
                        throw new \Exception("Vous ne pouvez effectuer cette action. Il faut vous identifier.");
                    }
                } catch (\Exception $e) {
                    $_SESSION['connexionException'] = $e->getMessage();
                    header('Location: index.php?action=connexion#exceptionMessage');
                }

                break;
            case 'myCommentsPage':
                try {
                    if (isset($_SESSION['pseudo'])) {
                        $datas['page'] = isset($_GET['page']) ? (int) $_GET['page'] : 1;
                        if (isset($_SESSION['commentException'])) {
                            $datas['commentException'] = $_SESSION['commentException'];
                            unset($_SESSION['commentException']);
                        }
                        $privateController = new PrivateController();
                        $privateController->myCommentsPage($datas);
                    } else {
                        throw new \Exception("Vous ne pouvez accéder à cette page. Il faut vous identifier.");
                    }
                } catch (\Exception $e) {
                    $_SESSION['connexionException'] = $e->getMessage();
                    header('Location: index.php?action=connexion#exceptionMessage');
                }

                break;
            case 'managePostsPage':
                if (isset($_SESSION['pseudo']) and $_SESSION['role'] === 'Admin') {
                    $datas['page'] = isset($_GET['page']) ? (int) $_GET['page'] : 1;
                    if (isset($_SESSION['exceptionMessage'])) {
                        $datas['exceptionMessage'] = $_SESSION['exceptionMessage'];
                        unset($_SESSION['exceptionMessage']);
                    }
                    $privateController = new PrivateController();
                    $privateController->managePostsPage($datas);
                } else {
                    throw new \Exception("Vous ne possédez pas les droits pour accéder à cette page.");
                }

                break;
            case 'confirmDeletionPage':
                    if (isset($_SESSION['pseudo']) and $_SESSION['role'] === 'Admin') {
                        $datas['entity'] = isset($_GET['entity']) ? (string) $_GET['entity'] : null;
                        $datas['id'] = isset($_GET['id']) ? (int) $_GET['id'] : null;
                        $datas['pseudo'] = isset($_GET['pseudo']) ? (string) $_GET['pseudo'] : null;
                        $privateController = new PrivateController();
                        $privateController->confirmDeletionPage($datas);
                    } else {
                        throw new \Exception("Vous ne possédez pas les droits pour accéder à cette page.");
                    }
    
                break;
            case 'deleteEntity':
                    if (isset($_SESSION['pseudo']) and $_SESSION['role'] === 'Admin') {
                        $datas['entity'] = isset($_GET['entity']) ? (string) $_GET['entity'] : null;
                        $datas['id'] = isset($_GET['id']) ? (int) $_GET['id'] : null;
                        $datas['pseudo'] = isset($_GET['pseudo']) ? (string) $_GET['pseudo'] : null;
                        if (isset($_GET['token']) && $_GET['token'] === $_SESSION['token']) {
                            $privateController = new PrivateController();
                            $privateController->deleteEntity($datas);
                        } else {
                            throw new \Exception("Vous ne pouvez effectuer cette action. Le jeton de session ne correspond pas.");
                        }
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
                    $privateController = new PrivateController();
                    $privateController->postFormPage($datas);
                } else {
                    throw new \Exception("Vous ne possédez pas les droits pour accéder à cette page.");
                }

                break;
            case 'sendPostForm':
                if (isset($_SESSION['pseudo']) and $_SESSION['role'] === 'Admin') {
                    if (isset($_POST['token']) && $_POST['token'] === $_SESSION['token']) {
                        $privateController = new PrivateController();
                        $privateController->sendPostForm($datas);
                    } else {
                        throw new \Exception("Vous ne pouvez effectuer cette action. Le jeton de session ne correspond pas.");
                    }
                } else {
                    throw new \Exception("Vous ne possédez pas les droits pour accéder à cette page.");
                }

                break;
            case 'manageCommentsPage':
                if (isset($_SESSION['pseudo']) and $_SESSION['role'] === 'Admin') {
                    $datas['page'] = isset($_GET['page']) ? (int) $_GET['page'] : 1;
                    $datas['success'] = isset($_GET['success']) ?  (int) $_GET['success'] : null;
                    if (isset($_SESSION['exceptionMessage'])) {
                        $datas['exceptionMessage'] = $_SESSION['exceptionMessage'];
                        unset($_SESSION['exceptionMessage']);
                    }
                    $privateController = new PrivateController();
                    $privateController->manageCommentsPage($datas);
                } else {
                    throw new \Exception("Vous ne possédez pas les droits pour accéder à cette page.");
                }

                break;
                case 'modifyCommentStatus':
                    if (isset($_SESSION['pseudo']) and $_SESSION['role'] === 'Admin') {
                        $datas['id'] = isset($_GET['id']) ? (int) $_GET['id'] : null;
                        $datas['status'] = isset($_GET['status']) ? (string) $_GET['status'] : null;
                        if (isset($_GET['token']) && $_GET['token'] === $_SESSION['token']) {
                            $privateController = new PrivateController();
                            $privateController->modifyCommentStatus($datas);
                        } else {
                            throw new \Exception("Vous ne pouvez effectuer cette action. Le jeton de session ne correspond pas.");
                        }
                    } else {
                        throw new \Exception("Vous ne possédez pas les droits pour accéder à cette page.");
                    }
    
                    break;
                case 'manageUsersPage':
                    if (isset($_SESSION['pseudo']) and $_SESSION['role'] === 'Admin') {
                        $datas['page'] = isset($_GET['page']) ? (int) $_GET['page'] : 1;
                        $datas['success'] = isset($_GET['success']) ?  (int) $_GET['success'] : null;
                        if (isset($_SESSION['exceptionMessage'])) {
                            $datas['exceptionMessage'] = $_SESSION['exceptionMessage'];
                            unset($_SESSION['exceptionMessage']);
                        }
                        $privateController = new PrivateController();
                        $privateController->manageUsersPage($datas);
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
