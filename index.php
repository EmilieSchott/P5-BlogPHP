<?php

session_start();

use EmilieSchott\BlogPHP\Controller\PrivateController;
use EmilieSchott\BlogPHP\Controller\PublicController;
use EmilieSchott\BlogPHP\Model\CommentManager;
use EmilieSchott\BlogPHP\Model\PostManager;
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

date_default_timezone_set('Etc/UTC');

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
                        if (isset($_GET['blogPage'])) {
                            $datas['blogPage'] = (int) $_GET['blogPage'];
                        }
                        if (isset($_GET['entry'])) {
                            $datas['entry'] = (int) $_GET['entry'];
                        }
                        if (isset($_SESSION['commentException'])) {
                            $datas['commentException'] = $_SESSION['commentException'];
                            unset($_SESSION['commentException']);
                        }
                        $publicController->post($postManager, $commentManager, $twig, $datas);
                    } else {
                        throw new Exception('aucun identifiant de billet n\'a été indiqué.');
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
                $privateController->connexionPage($twig, $datas);

                break;
            default:
                throw new Exception("L'action indiquée n'est pas valide.");

                break;
        }
    } else {
        throw new \Exception("Aucune action n'a été indiquée.");
    }
} catch (\Exception $e) {
    $_SESSION['homePageException'] = $e->getMessage();
    header('Location: index.php?action=homePage');
}
