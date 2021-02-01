<?php
require_once 'vendor/autoload.php';

// template twig loading :
$loader = new \Twig\Loader\FilesystemLoader(__DIR__ . '/view');
$twig = new \Twig\Environment($loader, [
    'cache' => false // __DIR__ . '/tmp',
]);

//Routing
echo $twig->render('homeView.twig');

