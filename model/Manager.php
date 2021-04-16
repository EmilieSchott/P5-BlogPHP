<?php

namespace EmilieSchott\BlogPHP\Model;

abstract class Manager
{
    protected $db;
    
    public function __construct()
    {
        $this->db = PDOFactory::getDbConnection();
    }
}
