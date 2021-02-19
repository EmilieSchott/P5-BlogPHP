<?php 

// Define namespace of this class :
namespace EmilieSchott\BlogPHP\Model;

// Import Emilie Schott other class needed into the global namespace :
use EmilieSchott\BlogPHP\Model\Hydrate;

// Load other class needed:
require_once __DIR__ . '/Hydrate.php';

// Define this class :
class Post extends Hydrate {

    // Constants : 
    private $id;
    private $usersId;
    private $title;
    private $standFirst;
    private $content;
    private $createdAt;
    private $updatedAt;
    private $author;
    private $picture;

    // Getters :
    public function id() {
        return $this->id;   
    }

    public function usersId() {
        return $this->usersId;   
    }

    public function title() {
        return $this->title;   
    }

    public function standFirst() {
        return $this->standFirst;   
    }

    public function content() {
        return $this->content;   
    }

    public function createdAt() {
        return $this->createdAt;  
    }

    public function updatedAt() {
        return $this->updatedAt;   
    }

    public function author() {
        return $this->author;   
    }
    
    public function picture() {
        return $this->picture;   
    }

    // Setters :
    public function setId(int $id) {
        if ($id > 0) {
            $this->id = $id;
        }   
    }

    public function setUsersId(int $usersId) {
        if ($usersId > 0) {
            $this->usersId = $usersId;
        }     
    }

    public function setTitle(string $title) {  
            $this->title = $title;
    }

    public function setStandFirst(string $standFirst) {   
        $this->standFirst = $standFirst;  
    }

    public function setContent(string $content) {
            $this->content = $content;
    }

    // faut utiliser une classe standard PHP appelé DATETIME
    public function setCreatedAt($createdAt) {
        if (\preg_match("#^[0-3][0-9]-[0-1][0-9]-20[0-9][0-9]$#",$createdAt)) {
            $this->createdAt = $createdAt;
        }   
    }

    // faut utiliser une classe standard PHP appelé DATETIME
    public function setUpdatedAt($updatedAt) {
        if (\preg_match("#^[0-3][0-9]-[0-1][0-9]-20[0-9][0-9] à [0-2][0-9]h[0-5][0-9]$#",$updatedAt)) {
            $this->updatedAt = $updatedAt;
        }   
    }

    public function setAuthor(string $author) {
        $this->author = $author;
    }
    
    public function setPicture(string $picture) {
        $this->picture = $picture;
    }

}