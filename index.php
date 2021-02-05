<?php


// twig :
require_once 'vendor/autoload.php';

$loader = new \Twig\Loader\FilesystemLoader(__DIR__ . '/view');
$twig = new \Twig\Environment($loader, [
    'cache' => false // __DIR__ . '/tmp',
]);

//controller :
require('controller/controller.php');

//Routing
if (isset($_GET['action'])) {
    switch ($_GET['action']) {
        case 'homePage':
            homePage($twig);
            break;

        case 'contactMe':
            contactMe();
            break;

        default:
            homepage($twig);
    }
}

else {
    homePage($twig);
}





