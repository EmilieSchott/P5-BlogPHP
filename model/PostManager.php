<?php 

namespace EmilieSchott\BlogPHP\Model;

use EmilieSchott\BlogPHP\Model\Manager;
use EmilieSchott\BlogPHP\Model\Post;

class PostManager extends Manager {

    public function getList(): array {
        $posts=[];
        $q=$this->db->query(
            'SELECT id, title, standfirst, createdAt, picture, pictureDescription 
            FROM posts ORDER BY id DESC'
        );
        while($datas=$q->fetch(\PDO::FETCH_ASSOC)) {
            $posts[] = new Post($datas);
        }
        $postsPages = $this->paginator->paginator($posts, 3); 
        return $postsPages;
    }

    public function accessPage(array $posts, int $page): array {
        $posts = $this->paginator->displayPage($posts, $page); 
        return $posts;
    }

    public function getPost(int $id): object {
        $q = $this->db->prepare('SELECT * FROM posts WHERE id = ?');
        $q->execute([$id]);
        $post = $q->fetch();
        $post = new Post($post); 
        return $post;
    }

    // Add a post

    //Modify a post

    //Delete a post
}