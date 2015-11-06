<?php

require_once("HTMLView.php");
require_once("controller.php");


$HTMLView = new HTMLView();
$controller = new Controller();

//Anropar metod som returnerar det som ska visas i HTMLView:s body
$htmlBody = $controller->control();

//Anropar metod för att eka ut htmlBody
$HTMLView->echoHTML($htmlBody);