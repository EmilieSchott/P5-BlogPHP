<?php
use EmilieSchott\BlogPHP\Controller\PublicController;
use EmilieSchott\BlogPHP\Model\PostManager;
use EmilieSchott\BlogPHP\Model\CommentManager;

require_once __DIR__ . '/vendor/autoload.php';

$loader = new \Twig\Loader\FilesystemLoader(__DIR__ . '/view');
$twig = new \Twig\Environment($loader, [
    'cache' => false // TO DO : "False" to replace by " __DIR__ . '/tmp' " to enable cache.
]);
$publicController=new PublicController();
$postManager = new PostManager();
$commentManager = new CommentManager();

date_default_timezone_set('Etc/UTC');

if (isset($_GET['action'])) {
    $action = htmlspecialchars($_GET['action']);
    switch ($action) { 
        case 'homePage':
            $publicController->homePage($twig, $action);
            break;

        case 'contactMe':
            $publicController->contactMe();
            break;

        case 'blog':
            $postsPages = $publicController->getPosts($postManager);
            $posts = $postsPages['datasPages'];
            $pagesNbr = $postsPages['pagesNbr'];

            if (isset($_GET['page']) && $_GET['page'] > 0 && $_GET['page'] <= $pagesNbr) {
                $page=(int) $_GET['page']; 
            }
            else {
                $page=1;
                header('Location: index.php?action=blog&page=1');
            }

            $publicController->blog($postManager, $twig, $page, $pagesNbr, $posts, $action);
            break;
       
        case 'post':
            if (isset($_GET['id'])) {
                $datas['id'] =(int) $_GET['id'];
                if (isset($_GET['blogPage'])) {
                    $datas['blogPage'] = (int) $_GET['blogPage'];
                } 
                if (isset($_GET['page'])) {
                    $datas['page'] = (int) $_GET['page'];
                } 
                if (isset($_GET['entry'])) {
                    $datas['entry'] = (int) $_GET['entry'];
                }
                $publicController->post($postManager, $commentManager, $twig, $datas);
            } else {
                header('Location: index.php?action=blog&page=1');           
            }
            break;

        case 'addComment':
            $publicController->addComment($commentManager, $comment);
            break;

        default:
            $publicController->homePage($twig, $action);
            break;
    }
}

else {
    $action = 'homePage';
    $publicController->homePage($twig, $action);
}





