<?php

namespace EmilieSchott\BlogPHP\Model;

class PDOFactory
{
    private static $database;

    public static function getDatabaseConnection()
    {
        if (self::$database instanceof \PDO) {
            return self::$database;
        }
        
        try {
            self::$database = new \PDO('mysql:host=localhost;dbname=p5_blog_php;port=3308;charset=utf8', 'root', '');
        } catch (PDOException $exception) {
            die('Erreur :' . $exception->getMessage());
        }

        return self::$database;
    }
}
