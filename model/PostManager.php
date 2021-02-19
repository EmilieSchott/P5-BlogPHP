<?php 

// Define namespace of this class :
namespace EmilieSchott\BlogPHP\Model;

// Import Emilie Schott other classes needed into the global namespace :
use EmilieSchott\BlogPHP\Model\Manager; 
use EmilieSchott\BlogPHP\Model\Post;

// Load other class needed :
require_once __DIR__ . '/Manager.php';
require_once __DIR__ . '/Post.php';

// Define this class :
class PostManager extends Manager {

    // Add a post

    //Get posts list
    public function getList() {
        $posts=[];
        $q=$this->db->query('SELECT id, title, standfirst, DATE_FORMAT(createdAt, "%d-%m-%Y") AS createdAt, picture FROM posts ORDER BY id DESC');
        while($datas=$q->fetch(\PDO::FETCH_ASSOC)) {
            $posts[] = new Post($datas);
        }
        return $posts;
    }

    //Get a post

    //Modify a post

    //Delete a post
}