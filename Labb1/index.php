<?php

require_once("HTMLView.php");
require_once("MovieController.php");
require_once("TableController.php");

session_start();

$HTMLView = new HTMLView();
$movieController = new MovieController();

if($movieController->movieChosen()){
    $tableController = new TableController();
    $htmlBody = $tableController->start();
} else {
    $htmlBody = $movieController->start();
}

$HTMLView->echoHTML($htmlBody);