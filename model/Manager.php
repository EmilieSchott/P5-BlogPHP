<?php

namespace EmilieSchott\BlogPHP\Model;

use EmilieSchott\BlogPHP\Paginator\Paginator;

abstract class Manager
{
    protected $db;
    protected $paginator;
    
    public function __construct()
    {
        $this->db = PDOFactory::getDbConnection();
        $this->paginator = new Paginator();
    }
}
