<?php

namespace EmilieSchott\BlogPHP\Model;

class Post extends Hydrate
{
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

    public function getId(): int
    {
        return $this->id;
    }

    public function getUsersId(): int
    {
        return $this->usersId;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getStandFirst(): string
    {
        return $this->standFirst;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function getCreatedAt(): string
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): string
    {
        return $this->updatedAt;
    }

    public function getAuthor(): string
    {
        return $this->author;
    }
    
    public function getPicture(): string
    {
        return $this->picture;
    }

    public function getPictureDescription(): string
    {
        return $this->pictureDescription;
    }

    public function setId(int $id)
    {
        $this->id = $id;
    }

    public function setUsersId(int $usersId)
    {
        $this->usersId = $usersId;
    }

    public function setTitle(string $title)
    {
        if (\strlen($title) <= 150) {
            $this->title = \nl2br(htmlspecialchars($title));
        } else {
            throw new \Exception("Le titre doit faire moins de 150 caractères.");
        }
    }

    public function setStandFirst(string $standFirst)
    {
        $this->standFirst = \nl2br(htmlspecialchars($standFirst));
    }

    public function setContent(string $content)
    {
        $this->content = \nl2br(htmlspecialchars($content));
    }

    public function setCreatedAt(string $createdAt)
    {
        $createdAtDateTime = new \DateTime($createdAt);
        $this->createdAt = $createdAtDateTime->format('d-m-Y');
    }

    public function setUpdatedAt(?string $updatedAt)
    {
        $updatedAtDateTime = new \DateTime($updatedAt);
        $this->updatedAt = $updatedAtDateTime->format('d-m-Y \à H\hi');
    }

    public function setAuthor(string $author)
    {
        if (\strlen($author) <= 45) {
            $this->author = htmlspecialchars($author);
        } else {
            throw new \Exception("Le nom de l'auteur doit faire moins de 45 caractères.");
        }
    }
    
    public function setPicture(string $picture)
    {
        if (\strlen($picture) <= 150) {
            $this->picture = htmlspecialchars($picture);
        } else {
            throw new \Exception("Le nom de l'image doit faire moins de 150 caractères.");
        }
    }

    public function setPictureDescription(string $pictureDescription)
    {
        if (\strlen($pictureDescription) <= 250) {
            $this->pictureDescription = htmlspecialchars($pictureDescription);
        } else {
            throw new \Exception("La description de l'image doit faire moins de 250 caractères.");
        }
    }
}
