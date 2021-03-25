<?php

namespace EmilieSchott\BlogPHP\Model;

class UserManager extends Manager
{
    public function getUser(string $email): object
    {
        $q = $this->db->prepare('SELECT * FROM users WHERE email = ?');
        $q->execute([$email]);
        $r = $q->fetch();
        $user = new User($r);

        return $user;
    }
}
