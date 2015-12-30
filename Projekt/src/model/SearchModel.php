<?php

require_once("DAL.php");
require_once("GAUrl.php");
require_once("BHLUrl.php");

class SearchModel
{
    private $dal;
    private $GAUrl;
    private $BHLUrl;


    //Konstruktor
    public function __construct() {
        $this->dal = new DAL();
        $this->GAUrl = new GAUrl();
        $this->BHLUrl = new BHLUrl();
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

    public function getFileResults($fileUrl) {
        $fileContents = file_get_contents($fileUrl);
        $results = json_decode($fileContents);
        return $results;
    }

    public function getAPIResults($url, $isGA) {
        $data = $this->dal->getData($url);
        $results = json_decode($data);
        if($isGA) {
            $items = $results->items;
        } else {
            $items = $results->Result;
        }
        return $items;
    }

    //http://stackoverflow.com/questions/11330480/strip-php-variable-replace-white-spaces-with-dashes
    public function trimParam($param) {
        $param = ltrim($param);
        $param = rtrim($param);
        $param = preg_replace("/[\s-]+/", " ", $param);
        $param = preg_replace("/[\s_]/", "+", $param);
        return $param;
    }

    public function changeLangValue($language) {
        if($language === "DUT") {
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

    public function getUrl($title, $author, $year, $language, $isGA) {
        $trTitle = '';
        $trAuthor = '';
        if(!empty($title) && empty($author)) {
            $trTitle = $this->trimParam($title);
            if(!empty($year) && $language !== "NONE") {
                if($isGA) {
                    $url = $this->GAUrl->getStart() . $this->GAUrl->getQuery() . $trTitle . $this->GAUrl->getYear() . $year . $this->GAUrl->getLang() . $language . $this->GAUrl->getEnd();
                } else {
                    $url = $this->BHLUrl->getStart() . $this->BHLUrl->getTitle() . $trTitle . $this->BHLUrl->getYear() . $year . $this->BHLUrl->getLang() . $language . $this->BHLUrl->getEnd();
                }
            } elseif(empty($year) && $language === "NONE") {
                if($isGA) {
                    $url = $this->GAUrl->getStart() . $this->GAUrl->getQuery() . $trTitle . $this->GAUrl->getEnd();
                } else {
                    $url = $this->BHLUrl->getStart() . $this->BHLUrl->getTitle() . $trTitle . $this->BHLUrl->getEnd();
                }
            } elseif(empty($year) && $language !== "NONE") {
                if($isGA) {
                    $url = $this->GAUrl->getStart() . $this->GAUrl->getQuery() . $trTitle . $this->GAUrl->getLang() . $language . $this->GAUrl->getEnd();
                } else {
                    $url = $this->BHLUrl->getStart() . $this->BHLUrl->getTitle() . $trTitle . $this->BHLUrl->getLang() . $language . $this->BHLUrl->getEnd();
                }
            } elseif(!empty($year) && $language === "NONE") {
                if($isGA) {
                    $url = $this->GAUrl->getStart() . $this->GAUrl->getQuery() . $trTitle . $this->GAUrl->getYear() . $year . $this->GAUrl->getEnd();
                } else {
                    $url = $this->BHLUrl->getStart() . $this->BHLUrl->getTitle() . $trTitle . $this->BHLUrl->getYear() . $year . $this->BHLUrl->getEnd();
                }
            }
        } elseif(empty($title) && !empty($author)) {
            $trAuthor = $this->trimParam($author);
            if(!empty($year) && $language !== "NONE") {
                if($isGA) {
                    $url = $this->GAUrl->getStart() . $this->GAUrl->getAuthQuery() . $trAuthor . $this->GAUrl->getYear() . $year . $this->GAUrl->getLang() . $language . $this->GAUrl->getEnd();
                } else {
                    $url = $this->BHLUrl->getStart() . $this->BHLUrl->getAuth() . $trAuthor . $this->BHLUrl->getYear() . $year . $this->BHLUrl->getLang() . $language . $this->BHLUrl->getEnd();
                }
            } elseif(empty($year) && $language === "NONE") {
                if($isGA) {
                    $url = $this->GAUrl->getStart() . $this->GAUrl->getAuthQuery() . $trAuthor . $this->GAUrl->getEnd();
                } else {
                    $url = $this->BHLUrl->getStart() . $this->BHLUrl->getAuth() . $trAuthor . $this->BHLUrl->getEnd();
                }
            } elseif(empty($year) && $language !== "NONE") {
                if($isGA) {
                    $url = $this->GAUrl->getStart() . $this->GAUrl->getAuthQuery() . $trAuthor . $this->GAUrl->getLang() . $language . $this->GAUrl->getEnd();
                } else {
                    $url = $this->BHLUrl->getStart() . $this->BHLUrl->getAuth() . $trAuthor . $this->BHLUrl->getLang() . $language . $this->BHLUrl->getEnd();
                }
            } elseif(!empty($year) && $language === "NONE") {
                if($isGA) {
                    $url = $this->GAUrl->getStart() . $this->GAUrl->getAuthQuery() . $trAuthor . $this->GAUrl->getYear() . $year . $this->GAUrl->getEnd();
                } else {
                    $url = $this->BHLUrl->getStart() . $this->BHLUrl->getAuth() . $trAuthor . $this->BHLUrl->getYear() . $year . $this->BHLUrl->getEnd();
                }
            }
        } elseif(!empty($title) && !empty($author)) {
            $trTitle = $this->trimParam($title);
            $trAuthor = $this->trimParam($author);
            if(!empty($year) && $language !== "NONE") {
                if($isGA) {
                    $url = $this->GAUrl->getStart() . $this->GAUrl->getQuery() . $trTitle . $this->GAUrl->getAuthQF() . $trAuthor . $this->GAUrl->getYear() . $year . $this->GAUrl->getLang() . $language . $this->GAUrl->getEnd();
                } else {
                    $url = $this->BHLUrl->getStart() . $this->BHLUrl->getTitle() . $trTitle . $this->BHLUrl->getAuth() . $trAuthor . $this->BHLUrl->getYear() . $year . $this->BHLUrl->getLang() . $language . $this->BHLUrl->getEnd();
                }
            } elseif(empty($year) && $language === "NONE") {
                if($isGA) {
                    $url = $this->GAUrl->getStart() . $this->GAUrl->getQuery() . $trTitle . $this->GAUrl->getAuthQF() . $trAuthor . $this->GAUrl->getEnd();
                } else {
                    $url = $this->BHLUrl->getStart() . $this->BHLUrl->getTitle() . $trTitle . $this->BHLUrl->getAuth() . $trAuthor . $this->BHLUrl->getEnd();
                }
            } elseif(empty($year) && $language !== "NONE") {
                if($isGA) {
                    $url = $this->GAUrl->getStart() . $this->GAUrl->getQuery() . $trTitle . $this->GAUrl->getAuthQF() . $trAuthor . $this->GAUrl->getLang() . $language . $this->GAUrl->getEnd();
                } else {
                    $url = $this->BHLUrl->getStart() . $this->BHLUrl->getTitle() . $trTitle . $this->BHLUrl->getAuth() . $trAuthor . $this->BHLUrl->getLang() . $language . $this->BHLUrl->getEnd();
                }
            } elseif(!empty($year) && $language === "NONE") {
                if($isGA) {
                    $url = $this->GAUrl->getStart() . $this->GAUrl->getQuery() . $trTitle . $this->GAUrl->getAuthQF() . $trAuthor . $this->GAUrl->getYear() . $year . $this->GAUrl->getEnd();
                } else {
                    $url = $this->BHLUrl->getStart() . $this->BHLUrl->getTitle() . $trTitle . $this->BHLUrl->getAuth() . $trAuthor . $this->BHLUrl->getYear() . $year . $this->BHLUrl->getEnd();
                }
            }
        }

        return $url;
    }

}