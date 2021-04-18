<?php

namespace EmilieSchott\BlogPHP\Model;

class CommentManager extends Manager
{
    public function getComments(int $id): array
    {
        $comments = [];
        $query=$this->db->prepare(
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

        $query=$this->db->prepare('INSERT INTO comments (postId, author, content) VALUES (:postId, :author, :content)');
        $query->bindValue(':postId', $comment->getPostId(), \PDO::PARAM_INT);
        $query->bindValue(':author', $comment->getAuthor(), \PDO::PARAM_STR);
        $query->bindValue(':content', $comment->getContent(), \PDO::PARAM_STR);
        $query->execute();
    }

    public function getUserCommments(string $pseudo): array
    {
        $comments = [];
        $query=$this->db->prepare(
            'SELECT id, postId, author, content, createdAt, status
            FROM comments WHERE author=? ORDER BY id DESC'
        );
        $query->execute([$pseudo]);
        while ($datas=$query->fetch()) {
            $comments[] = new Comment($datas);
        }

        return $comments;
    }

    public function deletePostComments(int $postId): void
    {
        $query = $this->db->prepare('DELETE * FROM comments WHERE postId = ?');
        $query->execute([$postId]);
    }
}
