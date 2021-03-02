<?php 

namespace EmilieSchott\BlogPHP\Model;

use EmilieSchott\BlogPHP\Model\Hydrate;

class Post extends Hydrate {

    private $id;
    private $usersId;
    private $title;
    private $standFirst;
    private $content;
    private $createdAt;
    private $updatedAt;
    private $author;
    private $picture;
    private $pictureDescription;

    public function getId(): int {
        return $this->id;   
    }

    public function getUsersId(): int {
        return $this->usersId;   
    }

    public function getTitle(): string {
        return $this->title;   
    }

    public function getStandFirst(): string {
        return $this->standFirst;   
    }

    public function getContent(): string {
        return $this->content;   
    }

    public function getCreatedAt(): string {
        return $this->createdAt;  
    }

    public function getUpdatedAt(): string {
        return $this->updatedAt;   
    }

    public function getAuthor(): string {
        return $this->author;   
    }
    
    public function getPicture(): string {
        return $this->picture;   
    }

    public function getPictureDescription(): string {
        return $this->pictureDescription;   
    }

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

    public function setCreatedAt(string $createdAt) {
        $createdAt = new \DateTime($createdAt);        
        $createdAt = $createdAt->format('d-m-Y');
        $this->createdAt = $createdAt;
    }

    public function setUpdatedAt(string $updatedAt) {
        $updatedAt = new \DateTime($updatedAt);        
        $updatedAt = $updatedAt->format('d-m-Y \à H\hi');
        $this->updatedAt = $updatedAt;
    }

    public function setAuthor(string $author) {
        $this->author = $author;
    }
    
    public function setPicture(string $picture) {
        $this->picture = $picture;
    }

    public function setPictureDescription(string $pictureDescription) {
        $this->pictureDescription = $pictureDescription;
    }
}