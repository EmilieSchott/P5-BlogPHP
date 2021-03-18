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
        $postsPages = $this->paginator->paginator($posts, 3);
        
        return $postsPages;
    }

    public function accessPage(array $postsPages, int $page): array
    {
        $postsPage = $this->paginator->displayPage($postsPages, $page);

        return $postsPage;
    }

    public function getPost(int $id): array
    {
        $post = [];
        $q = $this->db->prepare('SELECT * FROM posts WHERE id = ?');
        $q->execute([$id]);
        $r = $q->fetch();
        $post[] = new Post($r);

        return $post;
    }

    // Add a post

    //Modify a post

    //Delete a post
}
