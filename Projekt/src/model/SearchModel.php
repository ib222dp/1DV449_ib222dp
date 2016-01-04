<?php

require_once("DAL.php");
require_once("GAUrl.php");
require_once("BHLUrl.php");
require_once("BHLBook.php");
require_once("BHLAuthor.php");
require_once("GABook.php");

class SearchModel
{
    private $dal;
    private $GAUrl;
    private $BHLUrl;

    public function __construct() {
        $this->dal = new DAL();
        $this->GAUrl = new GAUrl();
        $this->BHLUrl = new BHLUrl();
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

    public function cmp($a, $b) {
        return strcmp($a->getTitle(), $b->getTitle());
    }

    public function createBHLBooks($items) {
        $books = array();
        if(!empty($items)) {
            foreach($items as $item) {
                $book = new BHLBook($item->TitleUrl, $item->Items[0]->ItemUrl, $item->FullTitle, $item->Edition, $item->PublisherPlace,
                    $item->PublisherName, $item->PublicationDate, $item->Items[0]->Contributor);
                foreach($item->Authors as $auth) {
                    $author = new BHLAuthor($auth->Name, $auth->Role);
                    $book->addAuthor($author);
                }
                array_push($books, $book);
            }

            foreach($books as $b) {
                $mainAuth;
                $newArray = array();
                foreach($b->getAuthors() as $auth) {
                    if(substr($auth->getRole(), 0, 4) === 'Main') {
                        $mainAuth = $auth;
                    } else {
                        array_push($newArray, $auth);
                    }
                }
                array_unshift($newArray, $mainAuth);
                $b->deleteAuthors();
                foreach($newArray as $author) {
                    $b->addAuthor($author);
                }
            }
            //http://stackoverflow.com/questions/4282413/sort-array-of-objects-by-object-fields
            usort($books, array($this, "cmp"));
        }
        return $books;
    }

    public function inputEmpty($title, $author) {
        if(empty($title) && empty($author)) {
            return true;
        } else {
            return false;
        }
    }

    public function createGABooks($items) {
        $books = array();
        if(!empty($items)) {
            foreach($items as $item) {
                $book = new GABook($item->guid, $item->edmIsShownAt[0], $item->title[0], $item->year[0], $item->dcCreator[0]);
                array_push($books, $book);
            }
            usort($books, array($this, "cmp"));
        }
        return $books;
    }

    //http://stackoverflow.com/questions/11330480/strip-php-variable-replace-white-spaces-with-dashes
    public function trimParam($param) {
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
        if(!empty($title)) {
            $trTitle = $this->trimParam($title);
        }
        if(!empty($author)) {
            $trAuthor = $this->trimParam($author);
        }

        if($isGA) {
            $start = $this->GAUrl->getStart();
            $titleQuery = $this->GAUrl->getQuery() . $trTitle;
            if(!empty($title) && !empty($author)) {
                $authQuery = $this->GAUrl->getAuthQF() . $trAuthor;
            } else {
                $authQuery = $this->GAUrl->getAuthQuery() . $trAuthor;
            }
            $yearQuery =  $this->GAUrl->getYear() . $year;
            $langQuery = $this->GAUrl->getLang() . $language;
            $end = $this->GAUrl->getEnd();
        } else {
            $start = $this->BHLUrl->getStart();
            $titleQuery = $this->BHLUrl->getTitle() . $trTitle;
            $authQuery = $this->BHLUrl->getAuth() . $trAuthor;
            $yearQuery = $this->BHLUrl->getYear() . $year;
            $langQuery = $this->BHLUrl->getLang() . $language;
            $end = $this->BHLUrl->getEnd();
        }

        if(!empty($title) && empty($author)) {
            if(!empty($year) && $language !== "NONE") {
                $middle = $titleQuery . $yearQuery . $langQuery;
            } elseif(empty($year) && $language === "NONE") {
                $middle = $titleQuery;
            } elseif(empty($year) && $language !== "NONE") {
                $middle = $titleQuery . $langQuery;
            } elseif(!empty($year) && $language === "NONE") {
                $middle = $titleQuery . $yearQuery;
            }
        } elseif(empty($title) && !empty($author)) {
            if(!empty($year) && $language !== "NONE") {
                $middle = $authQuery . $yearQuery . $langQuery;
            } elseif(empty($year) && $language === "NONE") {
                $middle = $authQuery;
            } elseif(empty($year) && $language !== "NONE") {
                $middle = $authQuery . $langQuery;
            } elseif(!empty($year) && $language === "NONE") {
                $middle = $authQuery . $yearQuery;
            }
        } elseif(!empty($title) && !empty($author)) {
            if(!empty($year) && $language !== "NONE") {
                $middle = $titleQuery . $authQuery . $yearQuery . $langQuery;
            } elseif(empty($year) && $language === "NONE") {
                $middle = $titleQuery . $authQuery;
            } elseif(empty($year) && $language !== "NONE") {
                $middle = $titleQuery . $authQuery . $langQuery;
            } elseif(!empty($year) && $language === "NONE") {
                $middle = $titleQuery . $authQuery . $yearQuery;
            }
        }
        $url = $start . $middle . $end;
        return $url;
    }

}