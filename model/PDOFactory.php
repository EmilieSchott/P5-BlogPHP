<?php

namespace EmilieSchott\BlogPHP\Model;

class PDOFactory
{
    private static $db;

    public static function getDbConnection()
    {
        if (self::$db instanceof \PDO) {
            return self::$db;
        }
        
        try {
            self::$db = new \PDO('mysql:host=localhost;dbname=p5_blog_php;port=3308;charset=utf8', 'root', '');
        } catch (PDOException $exception) {
            die('Erreur :' . $exception->getMessage());
        }

        return self::$db;
    }
}
