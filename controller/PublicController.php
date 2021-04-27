<?php

namespace EmilieSchott\BlogPHP\Controller;

use EmilieSchott\BlogPHP\Model\CommentManager;
use EmilieSchott\BlogPHP\Model\PostManager;
use EmilieSchott\BlogPHP\Paginator\Paginator;
use League\OAuth2\Client\Provider\Google;
use PHPMailer\PHPMailer\OAuth;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use Twig\Environment;

class PublicController
{
    use Paginator;

    public function homePage(Environment $twig, array $datas)
    {
        if (array_key_exists('send', $datas) && ($datas['send'] < 0 || $datas['send'] > 1)) {
            unset($datas['send']);
        }
        echo $twig->render('homeView.html.twig', $datas);
    }
    
    public function contactMe()
    {
        if (
            empty($_POST['name']) ||
            empty($_POST['firstName']) ||
            empty($_POST['email']) ||
            empty($_POST['message'])
        ) {
            throw new \Exception("Un ou plusieurs champs n'ont pas été rempli(s).");
        }

        if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
            throw new \Exception("L'adresse mail n'est pas valide.");
        }
            
        $name = htmlspecialchars($_POST['name']);
        $firstName = htmlspecialchars($_POST['firstName']);
        $email_address = htmlspecialchars($_POST['email']);
        $message = htmlspecialchars($_POST['message']);

        $mail = new PHPMailer();
        $mail->isSMTP();
        $mail->SMTPDebug = SMTP::DEBUG_OFF;
        $mail->Host = 'smtp.gmail.com';
        $mail->Port = 587;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->SMTPAuth = true;
        $mail->AuthType = 'XOAUTH2';

        // TODO: fill some blanks
        $email = '';
        $clientId = '';
        $clientSecret = '';
        $refreshToken = '';

        if (
            empty($email) ||
            empty($clientId) ||
            empty($clientSecret) ||
            empty($refreshToken)
        ) {
            throw new \Exception("Le formulaire de contact n'a pas été paramétré correctement.");
        }

        $provider = new Google([
            'clientId' => $clientId,
            'clientSecret' => $clientSecret,
        ]);

        $mail->setOAuth(new OAuth([
            'provider' => $provider,
            'clientId' => $clientId,
            'clientSecret' => $clientSecret,
            'refreshToken' => $refreshToken,
            'userName' => $email,
        ]));

        $mail->setFrom($email, 'Emilie Schott');
        $mail->addAddress('emilie.schott@gmail.com', 'Emilie Schott');
        $mail->Subject = '[Blog Web-Link] Formulaire de contact';
        $mail->CharSet = PHPMailer::CHARSET_UTF8;
        $mail->msgHTML('<!DOCTYPE html><html><body> <strong>De :</strong> ' . $name . ' ' . $firstName . ' - ' . $email_address . ' <br/><br/><strong>Message :</strong><br/>' . $message . ' </body></html>');

        if (!$mail->send()) {
            echo 'Mailer Error: ' . $mail->ErrorInfo;
        } else {
            echo 'Message sent!';
        }
        header('Location: index.php');
    }

    public function blog(Environment $twig, array $datas)
    {
        try {
            $postManager = new PostManager();
            $posts = $postManager->getList();
            $postsPages = $this->paginator($posts, 3);
            $datas['pagesNumber'] = $postsPages['pagesNumber'];
            $datas['posts'] =  $this->displayPage($postsPages, $datas['page']);
            echo $twig->render('blogView.html.twig', $datas);
        } catch (\Exception $e) {
            $datas['blogException'] = $e->getMessage();
            echo $twig->render('blogView.html.twig', $datas);
        }
    }
 
    public function post(Environment $twig, array $datas)
    {
        if ($datas['id'] > 0) {
            try {
                $this->getPostAndComments($datas);
            } catch (\Exception $e) {
                $datas['commentsException'] = $e->getMessage();
            }
            if (array_key_exists('success', $datas) && ($datas['success'] < 0 || $datas['success'] > 1)) {
                unset($datas['success']);
            }

            echo $twig->render('postView.html.twig', $datas);
        } else {
            throw new \Exception("L'identifiant du billet indiqué n'est pas valide.");
        }
    }

    private function getPostAndComments(array &$datas)
    {
        $postManager = new PostManager();
        $post = $postManager->getPost($datas['id']);
        $datas['post'] = $post;
        if (is_null($datas['post'])) {
            throw new \Exception("Le billet demandé n'a pas pu être récupéré.");
        }
        $commentManager = new CommentManager();
        $comments = $commentManager->getComments($datas['id']);
        if (!empty($comments)) {
            $commentsPages = $this->paginator($comments, 5);
            $datas['pagesNumber'] = $commentsPages['pagesNumber'];
            $datas['comments'] = $this->displayPage($commentsPages, $datas['page']);
        }
    }

    public function addComment(): void
    {
        $datas = [
            'author' => $_POST['author'],
            'content' => $_POST['content'],
            'postId' => $_POST['postId']
        ];
        $datas['status'] = $_SESSION['role'] === 'Admin'  ? 'Validé' : 'En attente';

        try {
            $commentManager = new CommentManager();
            $commentManager->addComment($datas);
        } catch (\PDOException $PDO) {
            header('Location: index.php?action=post&id=' . $datas['postId'] . '&success=0&#addComment');
        }
        header('Location: index.php?action=post&id=' . $datas['postId'] . '&success=1&#addComment');
    }
}
