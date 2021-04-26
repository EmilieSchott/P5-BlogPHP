<?php

namespace EmilieSchott\BlogPHP\Model;

class PostManager extends Manager
{
    public function getList(): array
    {
        $posts = [];
        $q=$this->db->query(
            'SELECT id, title, standfirst, createdAt, updatedAt, author, picture, pictureDescription 
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

    public function deletePost(int $id): void
    {
        $query = $this->db->prepare('DELETE FROM posts WHERE id = ?');
        $query->execute([$id]);
    }
    
    public function addPost(array $datas): void
    {
        $post = new Post($datas);
        $query=$this->db->prepare(
            'INSERT INTO posts (userId, title, standfirst, content, author, picture, pictureDescription) 
            VALUES (:userId, :title, :standfirst, :content, :author, :picture, :pictureDescription)'
        );
        $query->bindValue(':userId', $post->getUserId(), \PDO::PARAM_INT);
        $query->bindValue(':title', $post->getTitle(), \PDO::PARAM_STR);
        $query->bindValue(':standfirst', $post->getStandfirst(), \PDO::PARAM_STR);
        $query->bindValue(':content', $post->getContent(), \PDO::PARAM_STR);
        $query->bindValue(':author', $post->getAuthor(), \PDO::PARAM_STR);
        $query->bindValue(':picture', $post->getPicture(), \PDO::PARAM_STR);
        $query->bindValue(':pictureDescription', $post->getPictureDescription(), \PDO::PARAM_STR);
        $query->execute();
    }

    public function modifyPost(array $datas): void
    {
        $post = new Post($datas);

        $query=$this->db->prepare(
            'UPDATE posts 
            SET userId = :userId, title = :title, standfirst = :standfirst, content = :content, updatedAt = NOW(), author = :author, picture = :picture, pictureDescription = :pictureDescription
            WHERE id = :id'
        );
        $query->bindValue(':id', $post->getId(), \PDO::PARAM_INT);
        $query->bindValue(':userId', $post->getUserId(), \PDO::PARAM_INT);
        $query->bindValue(':title', $post->getTitle(), \PDO::PARAM_STR);
        $query->bindValue(':standfirst', $post->getStandfirst(), \PDO::PARAM_STR);
        $query->bindValue(':content', $post->getContent(), \PDO::PARAM_STR);
        $query->bindValue(':author', $post->getAuthor(), \PDO::PARAM_STR);
        $query->bindValue(':picture', $post->getPicture(), \PDO::PARAM_STR);
        $query->bindValue(':pictureDescription', $post->getPictureDescription(), \PDO::PARAM_STR);
        $query->execute();
    }
}
