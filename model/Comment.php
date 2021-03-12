<?php

namespace EmilieSchott\BlogPHP\Model;

class Comment extends Hydrate
{
    private $id;
    private $postId;
    private $author;
    private $content;
    private $createdAt;
    private $validated;

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

    public function getValidated(): bool
    {
        return $this->validated;
    }
    
    public function setId(int $id)
    {
        $secure = htmlspecialchars($id);
        if ($secure > 0) {
            $this->id = $secure;
        }
    }

    public function setPostId(int $postId)
    {
        $secure = htmlspecialchars($postId);
        if ($secure > 0) {
            $this->postId = $secure;
        }
    }

    public function setAuthor(string $author)
    {
        $secure = htmlspecialchars($author);
        $this->author = $secure;
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

    public function setValidated(bool $validated)
    {
        $secure = htmlspecialchars($validated);
        $this->validated = $secure;
    }
}
