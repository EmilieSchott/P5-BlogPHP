<?php

// twig and PHPmailer composer:
require_once __DIR__ . '/vendor/autoload.php';

$loader = new \Twig\Loader\FilesystemLoader(__DIR__ . '/view');
$twig = new \Twig\Environment($loader, [
    'cache' => false // __DIR__ . '/tmp',
]);

//controller :
require __DIR__ . '/controller/controller.php';

//Routing
if (isset($_GET['action'])) {
    switch ($_GET['action']) {
        case 'homePage':
            homePage($twig);
            break;

        case 'contactMe':
            contactMe();
            break;

        case 'blog':

            // NOUVELLE VERSION
            $postsPager=postsPager(); 
            $posts = $postsPager['posts'];
            $p_pages_nbr = $postsPager['p_pages_nbr'];

            if (isset($_GET['page']) && $_GET['page'] > 0 && $_GET['page'] <= $p_pages_nbr) {
                $page=(int) htmlspecialchars($_GET['page']); // htmlspecialchars nécessaire vu toutes les précautions du if ? Pense pas...
            }
            else {
                $page=1;
                header('Location: index.php?action=blog&page=1');
            }

 /*            if (isset($_GET['page']) && is_int($_GET['page'])===true && $_GET['page'] > 0 && $_GET['page'] <= $p_pages_nbr) {
                $page=htmlspecialchars($_GET['page']); 
                echo var_dump(__FILE__.' '. __LINE__);
            }   
            else {
                $page=1;
                echo var_dump(__FILE__.' '. __LINE__);
            }*/

            blog($twig, $page, $posts);
            break;

        default:
            homepage($twig);
    }
}

else {
    homePage($twig);
}





