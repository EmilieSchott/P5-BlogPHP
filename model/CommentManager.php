<?php

namespace EmilieSchott\BlogPHP\Model;

class CommentManager extends Manager
{
    public function getList(): array
    {
        $comments = [];
        $query=$this->database->query(
            'SELECT id, postId, author, content, createdAt, status 
            FROM comments WHERE status = "En attente" ORDER BY id'
        );
        while ($datas = $query->fetch(\PDO::FETCH_ASSOC)) {
            $comments[] = new Comment($datas);
        }

        if ($comments === null) {
            throw new \Exception("Aucun commentaire n'a pu être récupéré.");
        }
        
        return $comments;
    }

    public function getComments(int $id): array
    {
        $comments = [];
        $query=$this->database->prepare(
            'SELECT id, postId, author, content, createdAt, status 
            FROM comments WHERE postId=? AND status = "Validé" ORDER BY id'
        );
        $query->execute([$id]);
        while ($datas=$query->fetch()) {
            $comments[] = new Comment($datas);
        }

        return $comments;
    }

    public function addComment(array $datas): void
    {
        try {
            $comment = new Comment($datas);
        } catch (\Exception $e) {
            $_SESSION['commentException'] = $e->getMessage();
            header('Location: index.php?action=post&id=' . $datas['postId'] . '&entry=0&#addComment');
        }

        $query=$this->database->prepare('INSERT INTO comments (postId, author, content, status) VALUES (:postId, :author, :content, :status)');
        $query->bindValue(':postId', $comment->getPostId(), \PDO::PARAM_INT);
        $query->bindValue(':author', $comment->getAuthor(), \PDO::PARAM_STR);
        $query->bindValue(':content', $comment->getContent(), \PDO::PARAM_STR);
        $query->bindValue(':status', $comment->getStatus(), \PDO::PARAM_STR);
        $query->execute();
    }

    public function getUserCommments(string $pseudo): array
    {
        $comments = [];
        $query=$this->database->prepare(
            'SELECT id, postId, author, content, createdAt, status
            FROM comments WHERE author=? ORDER BY id DESC'
        );
        $query->execute([$pseudo]);
        while ($datas=$query->fetch()) {
            $comments[] = new Comment($datas);
        }

        return $comments;
    }

    public function modifyComment(Comment $comment, array $datasToModify): void
    {
        $comment->hydrate($datasToModify);

        $query = $this->database->prepare(
            'UPDATE comments 
            SET id = :id, postId = :postId, author =:author, content = :content, status = :status
            WHERE id = :id'
        );
        $query->bindValue(':id', $comment->getId(), \PDO::PARAM_INT);
        $query->bindValue(':postId', $comment->getPostId(), \PDO::PARAM_INT);
        $query->bindValue(':author', $comment->getAuthor(), \PDO::PARAM_STR);
        $query->bindValue(':content', $comment->getContent(), \PDO::PARAM_STR);
        $query->bindValue(':status', $comment->getStatus(), \PDO::PARAM_STR);
        $query->execute();
    }

    public function getComment(int $id): object
    {
        $query = $this->database->prepare(
            'SELECT * 
            FROM comments 
            WHERE Id=?'
        );
        $query->execute([$id]);

        if (false === ($data = $query->fetch())) {
            throw new \Exception("Ce commentaire n'existe pas.");
        }

        return new Comment($data);
    }
}
