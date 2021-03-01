<?php 

namespace EmilieSchott\BlogPHP\Model;

use EmilieSchott\BlogPHP\Model\Manager; 
use EmilieSchott\BlogPHP\Model\Post;

class PostManager extends Manager {

    public function getList(): array {
        $posts=[];
        $q=$this->db->query(
            'SELECT id, title, standfirst, 
            DATE_FORMAT(createdAt, "%d-%m-%Y") AS createdAt, 
            picture, pictureDescription 
            FROM posts ORDER BY id DESC'
        );
        while($datas=$q->fetch(\PDO::FETCH_ASSOC)) {
            $posts[] = new Post($datas);
        }
        return $posts;
    }

    // Add a post

    //Get a post

    //Modify a post

    //Delete a post
}