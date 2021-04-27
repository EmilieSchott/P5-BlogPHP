<?php

namespace EmilieSchott\BlogPHP\Controller;

use EmilieSchott\BlogPHP\Paginator\Paginator;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

abstract class Controller
{
    use Paginator;
    
    protected $twig;

    public function __construct()
    {
        $loader = new FilesystemLoader(__DIR__ . '/../view');
        $this->twig = new Environment($loader, [
            'cache' => false // "False" to replace by " __DIR__ . '/tmp' " to enable cache.
        ]);
    }
}
