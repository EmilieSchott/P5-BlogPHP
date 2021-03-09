<?php

namespace EmilieSchott\BlogPHP\Controller;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\OAuth;
use League\OAuth2\Client\Provider\Google;

class PublicController {
    public function homePage(object $twig, string $action) {
        echo $twig->render('homeView.html.twig', ['action' => $action]);
    }
    
    public function contactMe() {
        if (
            empty($_POST['name']) ||
            empty($_POST['firstName']) ||
            empty($_POST['email']) ||
            empty($_POST['message']) ||
            !filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)
        ) {
            echo "No arguments Provided!";
            return false;
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
        $mail->msgHTML('<!DOCTYPE html><html><body> <strong>De :</strong> ' . $name . ' ' . $firstName . ' - ' .  $email_address . ' <br/><br/><strong>Message :</strong><br/>' . $message . ' </body></html>');

        if (!$mail->send()) {
            echo 'Mailer Error: ' . $mail->ErrorInfo;
        } else {
            echo 'Message sent!';
        }
        header('Location: index.php');
    }

    public function getPosts(object $postManager): array {
        $posts = $postManager->getList();
        return $posts;
    }

    public function blog(object $postManager, object $twig, int $page, int $pagesNbr, array $posts, string $action) {  
        $posts = $postManager->accessPage($posts, $page); 
        echo $twig->render('blogView.html.twig', [
            'posts' => $posts, 
            'page' => $page, 
            'pages' => $pagesNbr, 
            'action' => $action
        ]);
    }
 
    public function post(object $postManager, object $commentManager, object $twig, array $datas) {
        if ($datas['id']>0) {
            $post = $postManager->getPost($datas['id']);
            $datas['post'] = $post[0];
            if (is_null($datas['post'])) {
                header('Location: index.php?action=blog&page=1');
            }

            $comments = $commentManager->getComments($datas['id']);
            if (!is_null($comments['pagesNbr'])) {
                $datas['pagesNbr'] = $comments['pagesNbr'];
                if (!array_key_exists('page', $datas) || $datas['page']<=0 || $datas['page']>$datas['pagesNbr']) {
                    $datas['page']=1;
                }
                $datas['comments'] = $commentManager->accessPage($comments['datasPages'], $datas['page']);
            } else {
                if (array_key_exists('page', $datas)) {
                    unset($datas['page']);
                }
            }
            
            if (array_key_exists('entry', $datas) && ($datas['entry']<0 || $datas['entry']>1)) {
                unset($datas['entry']);
            }

            echo $twig->render('postView.html.twig', $datas);
        } else {
            header('Location: index.php?action=blog&page=1');
        }
    }   

    public function addComment(object $commentManager): void {
        $comment = [
            'author' => $_POST['author'],
            'content' => $_POST['content'],
            'postId' => $_POST['postId']
        ];
        $entry=$commentManager->addComment($comment);
        if ($entry === false) {
            header('Location: index.php?action=post&id='.$comment['postId'].'&entry=0&#addComment');
        } else {
            header('Location: index.php?action=post&id='.$comment['postId'].'&entry=1&#addComment');
        }
    }
}