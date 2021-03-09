<?php 

namespace EmilieSchott\BlogPHP\Model;

use EmilieSchott\BlogPHP\Model\Hydrate;

class Comment extends Hydrate {

    private $id;
    private $postId;
    private $author;
    private $content;
    private $createdAt;
    private $validated;

    public function getId(): int {
        return $this->id;   
    }

    public function getPostId(): int {
        return $this->postId;   
    }

    public function getAuthor(): string {
        return $this->author;   
    }

    public function getContent(): string {
        return $this->content;   
    }

    public function getCreatedAt(): string {
        return $this->createdAt;  
    }

    public function getValidated(): bool {
        return $this->validated;  
    }
    
    public function setId(int $id) {
        if ($id > 0) {
            $this->id = $id;
        }   
    }

    public function setPostId(int $postId) {
        if ($postId > 0) {
            $this->postId = $postId;
        }     
    }

    public function setAuthor(string $author) {
        $this->author = $author;
    }

    public function setContent(string $content) {
        $this->content = $content;
    }

    public function setCreatedAt(string $createdAt) {
        $createdAt = new \DateTime($createdAt);        
        $createdAt = $createdAt->format('d-m-Y \à H\hi');
        $this->createdAt = $createdAt;
    }

    public function setValidated(bool $validated) {
        $this->validated = $validated;
    }

}