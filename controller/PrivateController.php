<?php

namespace EmilieSchott\BlogPHP\Controller;

class PrivateController
{
    public function connexionPage(object $twig, array $datas): void
    {
        echo $twig->render('connexionView.html.twig', $datas);
    }
}
