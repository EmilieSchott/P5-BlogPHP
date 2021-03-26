<?php

namespace EmilieSchott\BlogPHP\Model;

class UserManager extends Manager
{
    public function getUser(string $email): object
    {
        $query = $this->db->prepare('SELECT * FROM users WHERE email = ?');
        $query->execute([$email]);

        return $user = new User($query->fetch());
    }
}
