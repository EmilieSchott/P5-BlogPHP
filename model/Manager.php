<?php

namespace EmilieSchott\BlogPHP\Model;

class Manager {
    protected function dbConnect() {
        $db=new \PDO('mysql:host=localhost;dbname=p5_blog_php;port=3308;charset=utf8', 'root', '', array(\PDO::ATTR_ERRMODE=>\PDO::ERRMODE_EXCEPTION));
        return $db;
    }
}