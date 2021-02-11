<?php

//Import PHPMailer classes into the global namespace
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\OAuth;
// Alias the League Google OAuth2 provider class
use League\OAuth2\Client\Provider\Google;

function homePage($twig) {
    echo $twig->render('homeView.twig');
}

function contactMe() {
    if(
        empty($_POST['name']) ||
        empty($_POST['firstName']) ||
        empty($_POST['email']) ||
        empty($_POST['message']) ||
        !filter_var($_POST['email'],FILTER_VALIDATE_EMAIL)
    ){
        echo "No arguments Provided!";
        return false;
    }
        
    $name = htmlspecialchars($_POST['name']);
    $firstName = htmlspecialchars($_POST['firstName']);
    $email_address = htmlspecialchars($_POST['email']);
    $message = htmlspecialchars($_POST['message']);

    //SMTP needs accurate times, and the PHP time zone MUST be set
    //This should be done in your php.ini, but this is how to do it if you don't have access to that
    date_default_timezone_set('Etc/UTC');

    //Create a new PHPMailer instance
    $mail = new PHPMailer();

    //Tell PHPMailer to use SMTP
    $mail->isSMTP();

    //Enable SMTP debugging
    // SMTP::DEBUG_OFF = off (for production use)
    // SMTP::DEBUG_CLIENT = client messages
    // SMTP::DEBUG_SERVER = client and server messages
    $mail->SMTPDebug = SMTP::DEBUG_OFF;

    //Set the hostname of the mail server
    $mail->Host = 'smtp.gmail.com';

    //Set the SMTP port number - 587 for authenticated TLS, a.k.a. RFC4409 SMTP submission
    $mail->Port = 587;

    //Set the encryption mechanism to use - STARTTLS or SMTPS
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;

    //Whether to use SMTP authentication
    $mail->SMTPAuth = true;

    //Set AuthType to use XOAUTH2
    $mail->AuthType = 'XOAUTH2';

    //Fill in authentication details here
    //Either the gmail account owner, or the user that gave consent
    $email = '';
    $clientId = ''; 
    $clientSecret = ''; 

    //Obtained by configuring and running get_oauth_token.php
    //after setting up an app in Google Developer Console.
    $refreshToken = '';

    //Create a new OAuth2 provider instance
    $provider = new Google(
        [
            'clientId' => $clientId,
            'clientSecret' => $clientSecret,
        ]
    );

    //Pass the OAuth provider instance to PHPMailer
    $mail->setOAuth(
        new OAuth(
            [
                'provider' => $provider,
                'clientId' => $clientId,
                'clientSecret' => $clientSecret,
                'refreshToken' => $refreshToken,
                'userName' => $email,
            ]
        )
    );

    //Set who the message is to be sent from
    //For gmail, this generally needs to be the same as the user you logged in as
    $mail->setFrom($email, 'Emilie Schott');

    //Set who the message is to be sent to
    $mail->addAddress('emilie.schott@gmail.com', 'Emilie Schott');

    //Set the subject line
    $mail->Subject = '[Blog Web-Link] Formulaire de contact';

    //Read an HTML message body from an external file, convert referenced images to embedded,
    //convert HTML into a basic plain-text alternative body
    $mail->CharSet = PHPMailer::CHARSET_UTF8;
    $mail->msgHTML('<!DOCTYPE html><html><body> <strong>De :</strong> ' . $name . ' ' . $firstName . ' - ' .  $email_address . ' <br/><br/><strong>Message :</strong><br/>' . $message . ' </body></html>');

    //send the message, check for errors
    if (!$mail->send()) {
        echo 'Mailer Error: ' . $mail->ErrorInfo;
    } else {
        echo 'Message sent!';
    }

    header('Location: index.php');

}



