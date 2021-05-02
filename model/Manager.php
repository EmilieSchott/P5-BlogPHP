<?php

namespace EmilieSchott\BlogPHP\Model;

abstract class Manager
{
    protected $database;
    
    public function __construct()
    {
        $this->database = PDOFactory::getDatabaseConnection();
    }
}
