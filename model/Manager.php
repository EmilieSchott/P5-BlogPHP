<?php

namespace EmilieSchott\BlogPHP\Model;

use EmilieSchott\BlogPHP\Paginator\Paginator;
use EmilieSchott\BlogPHP\Model\PDOFactory;

abstract class Manager extends Paginator {
    protected $db;
    protected $paginator;
    
    public function __construct() {
        $this->db = PDOFactory::getDbConnection();
        $this->paginator = new Paginator(); 
    }
}