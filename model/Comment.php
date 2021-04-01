<?php

namespace EmilieSchott\BlogPHP\Model;

use Exception;

class Comment extends Hydrate
{
    private $id;
    private $postId;
    private $author;
    private $content;
    private $createdAt;
    private $status;

    public function getId(): int
    {
        return $this->id;
    }

    public function getPostId(): int
    {
        return $this->postId;
    }

    public function getAuthor(): string
    {
        return $this->author;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function getCreatedAt(): string
    {
        return $this->createdAt;
    }

    public function getstatus(): string
    {
        return $this->status;
    }
    
    public function setId(int $id)
    {
        $this->id = $id;
    }

    public function setPostId(int $postId)
    {
        $this->postId = $postId;
    }

    public function setAuthor(string $author)
    {
        if (\strlen($author) <= 45) {
            $secure = htmlspecialchars($author);
            $this->author = $secure;
        } else {
            throw new Exception('Le nom de l\'auteur doit faire moins de 45 caractères.');
        }
    }

    public function setContent(string $content)
    {
        $secure = htmlspecialchars($content);
        $this->content = $secure;
    }

    public function setCreatedAt(string $createdAt)
    {
        $createdAt = new \DateTime($createdAt);
        $createdAt = $createdAt->format('d-m-Y \à H\hi');
        $this->createdAt = $createdAt;
    }

    public function setstatus(string $status)
    {
        $this->status = $status;
    }
}
