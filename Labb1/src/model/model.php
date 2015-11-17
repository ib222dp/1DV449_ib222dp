<?php

require_once("DAO.php");

abstract class Model
{
    private $dao;
    protected static $day1 = "01";
    protected static $day2 = "02";
    protected static $day3 = "03";

    //Konstruktor
    public function __construct() {
        $this->dao = new DAO();
    }

    //Hämtar url i sessionsvariabel
    public function getSavedURL(){
        return $_SESSION["givenURL"];
    }

    //Sätter url som sessionsvariabel
    public function setURL($url) {
        $_SESSION["givenURL"] = $url;
    }

    //Kollar om sessionsvariabel givenURL är tom
    public function URLIsSet(){
        $url = $this->getSavedURL();
        if(isset($url) && !empty($url)) {
            return true;
        } else {
            return false;
        }
    }

    //Hämtar filmer i sessionsvariabel
    public function getSavedMovies() {
        return $_SESSION["movies"];
    }

    //Sätter filmer som sessionsvariabel
    public function setMovies($movies) {
        $_SESSION["movies"] = $movies;
    }

    //Kollar om sessionsvariabel movies är tom
    public function moviesAreSet(){
        $movies = $this->getSavedMovies();
        if(isset($movies) && !empty($movies)) {
            return true;
        } else {
            return false;
        }
    }

    //Förstör sessionen
    public function destroySession() {
        $_SESSION = array();
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $params["path"],
                $params["domain"], $params["secure"], $params["httponly"]);
        }
        session_destroy();
    }

    //Postar formulärdata och returnerar svaret
    public function postData($formURL, $postFields) {
        $data = $this->dao->postData($formURL, $postFields);
        return $data;
    }

    //Hämtar data via curl-anrop och returnerar json eller xpath
    public function getResponse($url, $isJson) {
        $dataAndURL = $this->dao->getDataAndURL($url);
        if($isJson){
            return $dataAndURL[0];
        } else {
            $dom = new DOMDocument();
            if ($dom->loadHTML($dataAndURL[0])) {
                $xpath = new DOMXPath($dom);
                return $xpath;
            } else {
                die("<a href='index.php'>Tillbaka</a><p>Något gick fel.</p>");
            }
        }
    }

    //Hämtar den länk som omdirigeras till när man klickar på ett menyalternativ på startsidan
    public function getMenuRedirect($menuItem) {
        $menuLink = $this->getSavedURL() . $menuItem->getAttribute("href");
        $dataAndURL = $this->dao->getDataAndURL($menuLink);
        $menuURL = rtrim($dataAndURL[1], '/') . '/';
        return $menuURL;
    }

    //Hämtar menyalternativen på startsidan
    //och den länk som omdirigeras till när man klickar på ett menyalternativ på startsidan
    public function getMenuLink($itemNo) {
        $xpath = $this->getResponse($this->getSavedURL(), false);
        $menuLinks = $xpath->query('//a');
        $menuLink = $this->getMenuRedirect($menuLinks->item($itemNo));
        return $menuLink;
    }

}
