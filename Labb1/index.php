<?php

require_once("HTMLView.php");
require_once("src/controller/MovieController.php");
require_once("src/controller/TableController.php");

session_start();

$HTMLView = new HTMLView();
$movieController = new MovieController();

//Kontrollerar om användaren har valt en film
if($movieController->movieChosen()){
    $tableController = new TableController();
    $htmlBody = $tableController->start();
} else {
    $htmlBody = $movieController->start();
}

$HTMLView->echoHTML($htmlBody);