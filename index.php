<?php
use EmilieSchott\BlogPHP\Model\PostManager;
use EmilieSchott\BlogPHP\Controller\PublicController;

require_once __DIR__ . '/vendor/autoload.php';

$loader = new \Twig\Loader\FilesystemLoader(__DIR__ . '/view');
$twig = new \Twig\Environment($loader, [
    'cache' => false // "False" to replace by " __DIR__ . '/tmp' " to enable cache.
]);
$publicController=new PublicController();
$postManager = new PostManager();

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

        default:
        $publicController->homePage($twig, $action);
    }
}

else {
    $action = 'homePage';
    $publicController->homePage($twig, $action);
}





