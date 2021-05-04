# A blog website with homepage to tell about you, blog index and administration pannel

[![Codacy Badge](https://api.codacy.com/project/badge/Grade/990e76ea822c4f2ea2676140e1320b82)](https://app.codacy.com/gh/EmilieSchott/P5-BlogPHP?utm_source=github.com&utm_medium=referral&utm_content=EmilieSchott/P5-BlogPHP&utm_campaign=Badge_Grade_Settings)

========================================================================================

## Overview of the project
--------------------------
You can consult digramms folder to see UML diagramms of the project.

## Installation
----------------

1) Clone this repository on your computer. 

2) Run Composer, place yourself in the folder where you cloned this repository and run "composer install" command. 

3) To create the database, tables and datas : 
- Use phpmyadmin to import database/p5_blog_php.sql 
OR 
- Use a CLI, place you in the folder where is your current version mySQL on your computer (ex : C:\wamp64\bin\mysql\mysql8.0.18\bin\mysql) and run this command line : 
"mysql -u *username* -p < path\to\your\file\p5_blog_php.sql"
(ex : "mysql -u root -p < C:\wamp64\www\projects\p5-blogphp\database\p5_blog_php.sql")

4) Change connexion to database informations in Manager/PDOFactory.php

5) Configure contact form sending in controller/PublicController.php in contactMe() method.

## Don't forget to personnalize the blog if you want use it for personnal purpose 
-----------------------------------------------------------------------------------

1)Change title, the surtitle of the blog in /view/base.html.twig

2)Replace homepage photo (in public/theme/img/portrait ES.jpg) by your own photo or logo and change name of the file in view/homeView.html.twig.

3)Replace name, moto and text by your owns in view/homeView.html.twig.

4)Replace homepage CV (in public/download//CV-Emilie_Schott.pdf) by your own CV and change name of the file in view/homeView.html.twig.

5)Change links to your social networks in  pathway in 'view/homeView.html.twig.

6)Create your own admin account.

7)Connect you with the admin count by default :
		- pseudo : Emilie
		- password : test

8)Go in "Mon Compte" (= my account)  > "G�rer les utilisateurs" (= manage users)  > click on "Modifier les informations" (= modify datas) in the section regarding your new account. > click on "Passer l'utilisateur au statut "admin"".

9)disconnect yourself.

10)Connect you with your new admin account

11)Go in "Mon Compte" (= my account) > "G�rer les utilisateurs" (= manage users) > click on "supprimer l'utilisateur" (= delete user) in the section regarding the admin account "Emilie" > click on "oui, supprimer !" (= yes, to delete !)

## Copyrights
-------------

All images and pictures in public/upload/img/post are under domain public also public/theme/img/geralt-pixabay-web-4667382_1920.jpg.

On the other hand :
	- public/theme/img/portrait ES.jpg 
	- and public/download/CV-Emilie_Schott.pdf 
are my property and cannot be used. 

