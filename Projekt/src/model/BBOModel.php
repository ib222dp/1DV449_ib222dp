<?php

abstract class BBOModel
{

    public function __construct() {

    }

    public function destroySession() {
        $_SESSION = array();
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $params["path"],
                $params["domain"], $params["secure"], $params["httponly"]);
        }
        session_destroy();
    }

    public function getErrorMessage() {
        return $_SESSION['errormsg'];
    }

    public function setErrorMessage($message) {
        $_SESSION['errormsg'] = $message;
    }

    public function inputEmpty($title, $author) {
        if(empty($title) && empty($author)) {
            $this->setErrorMessage('You have to provide at least a title or an author');
            return true;
        } else {
            return false;
        }
    }

    public function yearOk($year) {
        if(strlen($year) === 4 || empty($year)) {
            if(is_numeric(substr($year, 0, 4)) || empty($year)) {
                return true;
            } else {
                $this->setErrorMessage('Provide year in format YYYY');
                return false;
            }
        } else {
            $this->setErrorMessage('Provide year in format YYYY');
            return false;
        }
    }

    public function changeLangValue($language) {
        if($language === "CHI") {
            $language = "zh";
        } elseif($language === "DUT") {
            $language = "nl";
        } elseif($language === "ENG") {
            $language = "en";
        } elseif($language === "FRE") {
            $language = "fr";
        } elseif($language === "GER") {
            $language = "de";
        } elseif($language === "ITA") {
            $language = "it";
        } elseif($language === "RUS") {
            $language = "ru";
        } elseif($language === "SPA") {
            $language = "es";
        }
        return $language;
    }

    public function getSavedBHLBooks() {
        return $_SESSION['BHLBooks'];
    }

    public function getSavedGABooks() {
        return $_SESSION['GABooks'];
    }

    public function saveResultsinSession($BHLBooks, $GABooks) {
        $_SESSION['BHLBooks'] = $BHLBooks;
        $_SESSION['GABooks'] = $GABooks;
    }

    public function getFileResults($fileUrl) {
        $fileContents = file_get_contents($fileUrl);
        $results = json_decode($fileContents);
        return $results;
    }

}