<?php

namespace EmilieSchott\BlogPHP\Model;

class CommentManager extends Manager
{
    public function getComments(int $id): array
    {
        $comments = [];
        $q=$this->db->prepare(
            'SELECT id, posts_id, author, content, createdAt, validated 
            FROM comments WHERE posts_id=? AND validated=1 ORDER BY id'
        );
        $q->execute([$id]);
        while ($datas=$q->fetch()) {
            $comments[] = new Comment($datas);
        }
        $commentsPages = $this->paginator->paginator($comments, 5);

        return $commentsPages;
    }

    public function accessPage(array $commentsPages, int $page)
    {
        $commentsPage = $this->paginator->displayPage($commentsPages, $page);

        return $commentsPage;
    }

    public function addComment(array $datas): void
    {
        try {
            $comment = new Comment($datas);
        } catch (\Exception $e) {
            $_SESSION['commentException'] = $e->getMessage();
            header('Location: index.php?action=post&id=' . $datas['postId'] . '&entry=0&#addComment');
        }

        $q=$this->db->prepare('INSERT INTO comments (posts_id, author, content) VALUES (:postId, :author, :content)');
        $q->bindValue(':postId', $comment->getPostId(), \PDO::PARAM_INT);
        $q->bindValue(':author', $comment->getAuthor(), \PDO::PARAM_STR);
        $q->bindValue(':content', $comment->getContent(), \PDO::PARAM_STR);
        $q->execute();
    }

    //Delete a comment
}
