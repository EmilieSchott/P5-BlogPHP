<?php

// Define namespace of this class :
namespace EmilieSchott\BlogPHP\Model;

// Define this class :
abstract class Manager {
    
    protected $db;
    
    public function __construct() {
        $db=new \PDO('mysql:host=localhost;dbname=p5_blog_php;port=3308;charset=utf8', 'root', '', array(\PDO::ATTR_ERRMODE=>\PDO::ERRMODE_EXCEPTION));
        $this->db=$db;
    }
}
