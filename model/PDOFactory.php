<?php

namespace EmilieSchott\BlogPHP\Model;

class PDOFactory {
    private static $db;

    public static function getDbConnection() {
        if (self::$db instanceof \PDO) {
            return self::$db;
        }
        self::$db = new \PDO('mysql:host=localhost;dbname=p5_blog_php;port=3308;charset=utf8', 'root', '');
        return self::$db;
    }
}