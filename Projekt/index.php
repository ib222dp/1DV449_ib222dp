<?php

require_once("MainView.php");
require_once("src/controller/SearchController.php");

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$MainView = new MainView();
$searchController = new SearchController();

$htmlBody = $searchController->start();

$MainView->echoHTML($htmlBody[0], $htmlBody[1]);