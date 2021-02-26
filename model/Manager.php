<?php

namespace EmilieSchott\BlogPHP\Model;

abstract class Manager {
    protected $db;
    
    public function __construct() {
        $db=new \PDO('mysql:host=localhost;dbname=p5_blog_php;port=3308;charset=utf8', 'root', '');
        $this->db=$db;
    }

    public function paginator(array $datas, int $itemsPerPage): array {
        $datasPages = \array_chunk($datas, $itemsPerPage);
        \array_unshift($datasPages, '');
        unset($datasPages[0]);
        $pagesNbr = \array_key_last($datasPages);

        $paginator = [
        'datasPages' => $datasPages,
        'pagesNbr' => $pagesNbr
        ];

        return $paginator;
    }

    public function displayPage(array $datasPages, int $page): array {
    $offset=$page-1;
    $datasPage=array_slice($datasPages, $offset, 1);
    $datasPage=$datasPage[0];
    return $datasPage;
    }
}