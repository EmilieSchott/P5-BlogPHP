<?php 

namespace EmilieSchott\BlogPHP\Model;

use EmilieSchott\BlogPHP\Model\Manager; 
use EmilieSchott\BlogPHP\Model\Comment;

class CommentManager extends Manager {
    public function getComments(int $id): array {
        $comments=[];
        $q=$this->db->prepare(
            'SELECT id, posts_id, author, content, createdAt, validated 
            FROM comments WHERE posts_id=? AND validated=1 ORDER BY id'
        );
        $q->execute([$id]);
        while($datas=$q->fetch()) {
            $comments[] = new Comment($datas);
        }
        $commentsPages = $this->paginator->paginator($comments, 5);

        return $commentsPages;
    }

    public function accessPage(array $commentsPages, int $page) {
        $commentsPage = $this->paginator->displayPage($commentsPages, $page);
        return $commentsPage;
    }

    public function addComment(array $comment): void {
        $q=$this->db->prepare('INSERT INTO comments (posts_id, author, content) VALUES (:postId, :author, :content)');
        $q->execute([
            'postId' => $comment['postId'],
            'author' => $comment['author'], 
            'content' => $comment['content']
        ]);
    }

    //Delete a comment
}