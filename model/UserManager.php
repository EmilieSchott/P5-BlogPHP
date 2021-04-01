<?php

namespace EmilieSchott\BlogPHP\Model;

class UserManager extends Manager
{
    public function getUser(string $pseudo)
    {
        $query = $this->db->prepare('SELECT * FROM users WHERE pseudo = ?');
        $query->execute([$pseudo]);

        $datas = $query->fetch();

        return !empty($datas) ? new User($datas) : false;
    }

    public function addUser(array $datas): void
    {
        try {
            $user = new User($datas);
        } catch (\Exception $e) {
            $_SESSION['inscriptionException'] = $e->getMessage();
            header('Location: index.php?action=inscription#exceptionMessage');
        }

        $query=$this->db->prepare('INSERT INTO users (role, pseudo, name, firstName, email, password) VALUES (:role, :pseudo, :name, :firstName, :email, :password)');
        $query->bindValue(':role', $user->getRole(), \PDO::PARAM_STR);
        $query->bindValue(':pseudo', $user->getPseudo(), \PDO::PARAM_STR);
        $query->bindValue(':name', $user->getName(), \PDO::PARAM_STR);
        $query->bindValue(':firstName', $user->getFirstName(), \PDO::PARAM_STR);
        $query->bindValue(':email', $user->getEmail(), \PDO::PARAM_STR);
        $query->bindValue(':password', $user->getPassword(), \PDO::PARAM_STR);

        $query->execute();
    }

    public function modifyUser($user, $modification): object
    {
        try {
            $user->hydrate($modification);

            $query=$this->db->prepare(
                'UPDATE users 
                SET role = :role, name = :name, firstName = :firstName, email = :email, password = :password 
                WHERE pseudo = :pseudo'
            );
            $query->bindValue(':role', $user->getRole(), \PDO::PARAM_STR);
            $query->bindValue(':pseudo', $user->getPseudo(), \PDO::PARAM_STR);
            $query->bindValue(':name', $user->getName(), \PDO::PARAM_STR);
            $query->bindValue(':firstName', $user->getFirstName(), \PDO::PARAM_STR);
            $query->bindValue(':email', $user->getEmail(), \PDO::PARAM_STR);
            $query->bindValue(':password', $user->getPassword(), \PDO::PARAM_STR);

            $query->execute();

            return $user;
        } catch (\Exception $e) {
            $_SESSION['modificationException'] = $e->getMessage();
            header('Location: index.php?action=modifyMyDatas#message');
        }
    }
}
