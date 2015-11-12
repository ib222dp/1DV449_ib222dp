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

    public function getSavedURL(){
        return $_SESSION["givenURL"];
    }

    //Sätter url som sessionsvariabel
    public function setURL($url) {
        $_SESSION["givenURL"] = $url;
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

    public function getPageAndURL($url) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_USERAGENT, "ib222dp");
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $page = curl_exec($ch);
        $redirectURL = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
        curl_close($ch);
        return array($page, $redirectURL);
    }


    public function getXPath($url) {
        $pageAndURL = $this->getPageAndURL($url);
        $dom = new DOMDocument();
        if($dom->loadHTML($pageAndURL[0])){
            $xpath = new DOMXPath($dom);
            return $xpath;
        } else {
            die("Något gick fel.");
        }
    }

    public function buildURL($menuItem) {
        $menuLink = $this->getSavedURL() . $menuItem->getAttribute("href");
        $pageAndURL = $this->getPageAndURL($menuLink);
        return $pageAndURL[1];
    }

    public function getMenuLinks() {
        $xpath = $this->getXPath($this->getSavedURL());
        $menuLinks = $xpath->query('//a');
        return $menuLinks;
    }

    public function getMenuLink($itemNo) {
        $menuLinks = $this->getMenuLinks();
        $menuLink = $this->buildURL($menuLinks->item($itemNo));
        return $menuLink;
    }

}
