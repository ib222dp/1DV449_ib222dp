<?php

abstract class Model
{
    //Konstruktor
    public function __construct() {

    }

    public function inputOK($url){
        if(empty($url)){
            return false;
        }else{
            return true;
        }
    }

    //Sätter url som sessionsvariabel
    public function setURL($url) {
        $_SESSION["givenURL"] = $url;
    }

    public function destroySession() {
        $_SESSION = array();

        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $params["path"], $params["domain"], $params["secure"], $params["httponly"]);
        }
        session_destroy();
    }

    public function getPage($url) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $page = curl_exec($ch);
        curl_close($ch);
        return $page;
    }

    public function getXPath($url) {
        $page = $this->getPage($url);
        $dom = new DOMDocument();

        if($dom->loadHTML($page)){
            $xpath = new DOMXPath($dom);
            return $xpath;
        } else {
            die("Fel");
        }
    }

    public function buildURL($menuLink) {
        $url = $_SESSION["givenURL"] . $menuLink->getAttribute("href") . "/";
        return $url;
    }

    public function getMenuLinks($url) {
        $xpath = $this->getXPath($url);
        $menuLinks = $xpath->query('//a');
        return $menuLinks;
    }

}
