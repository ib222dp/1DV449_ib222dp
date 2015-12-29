<?php

require_once("MainView.php");
require_once("src/controller/SearchController.php");

session_start();

$MainView = new MainView();
$searchController = new SearchController();

$htmlBody = $searchController->start();

$MainView->echoHTML($htmlBody);