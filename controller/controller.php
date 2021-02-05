<?php

function homePage($twig) {
    echo $twig->render('homeView.twig');
}

function contactMe() {
    // freelancer bootstrap theme code :
    if(empty($_POST['name']) ||
    empty($_POST['firstName']) ||
    empty($_POST['email']) ||
    empty($_POST['message']) ||
    !filter_var($_POST['email'],FILTER_VALIDATE_EMAIL))
    {
    var_dump(__FILE__ . ' . ' . __LINE__);
    echo "No arguments Provided!";
    var_dump(__FILE__ . ' . ' . __LINE__);
    return false;
    }
        
    $name = strip_tags(htmlspecialchars($_POST['name']));
    $email_address = strip_tags(htmlspecialchars($_POST['email']));
    $firstName = strip_tags(htmlspecialchars($_POST['firstName']));
    $message = strip_tags(htmlspecialchars($_POST['message']));
        
    // Create the email and send the message
    $to = 'emilie.schott@gmail.com'; 
    $email_subject = "Formulare de contact du blog: $name";
    $email_body = "Vous avez reçu un nouveau message du formulaire de contact de votre blog.\n\n"."Voilà les informations:\n\nNom: $name\n\nEmail: $email_address\n\nPrénom: $firstName\n\nMessage:\n$message";
    $headers = "From: noreply@lianna.orchal.net\n";
    $headers .= "Reply-To: $email_address";	
    mail($to,$email_subject,$email_body,$headers);
    return true;			
}