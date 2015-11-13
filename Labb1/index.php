<?php

require_once("HTMLView.php");
require_once("src/controller/MovieController.php");
require_once("src/controller/TableController.php");

session_start();

$HTMLView = new HTMLView();
$movieController = new MovieController();

//Startar controller eller dirigerar om till startsidan beroende på vilka query-parametrar som finns i url:en
if(!count($_GET) || $movieController->moviesListed()) {
    $htmlBody = $movieController->start();
} elseif($movieController->dayAndTimeChosen() || $movieController->bookTimeChosen()) {
    $tableController = new TableController();
    $htmlBody = $tableController->start();
} else {
    header('location: ' . $_SERVER['PHP_SELF']);
    die;
}

$HTMLView->echoHTML($htmlBody);