<?php

namespace EmilieSchott\BlogPHP\Model;

class PostManager extends Manager
{
    public function getList(): array
    {
        $posts = [];
        $q=$this->db->query(
            'SELECT id, title, standfirst, createdAt, picture, pictureDescription 
            FROM posts ORDER BY id DESC'
        );
        while ($datas = $q->fetch(\PDO::FETCH_ASSOC)) {
            $posts[] = new Post($datas);
        }

        if ($posts === null) {
            throw new \Exception("Aucun billet n'a pu être récupéré.");
        }
        
        return $posts;
    }

    public function getPost(int $id): object
    {
        $query = $this->db->prepare('SELECT * FROM posts WHERE id = ?');
        $query->execute([$id]);

        if (false === ($data = $query->fetch())) {
            throw new \Exception("Ce post n'existe pas.");
        }

        return new Post($data);
    }

    // Add a post

    //Modify a post

    //Delete a post
}
