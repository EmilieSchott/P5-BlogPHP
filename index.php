<?php
require_once 'vendor/autoload.php';

//Routing



// template twig loading :
$loader = new \Twig\Loader\FilesystemLoader(__DIR__ . '/view');
$twig = new \Twig\Environment($loader, [
    'cache' => false // __DIR__ . '/tmp',
]);

// en attendant de le mettre dans le contrôleur
echo $twig->render('homeView.twig');
