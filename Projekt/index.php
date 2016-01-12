<?php
require_once("MainHTMLView.php");
require_once("src/controller/MainController.php");

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$mainHTMLView = new MainHTMLView();
$mainController = new MainController();
$htmlBody = $mainController->start();
$mainHTMLView->echoHTML($htmlBody[0], $htmlBody[1]);