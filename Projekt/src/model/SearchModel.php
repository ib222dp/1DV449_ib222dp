<?php

require_once("DAL.php");
require_once("GAUrl.php");
require_once("BHLUrl.php");
require_once("BHLBook.php");
require_once("BHLAuthor.php");
require_once("GABook.php");
require_once("GAAuthor.php");

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

    public function searchTermInDB($title) {
        $result = $this->dal->searchTermInDB($title);
        if($result !== null) {
            //if(($result->saved_date - time()) <= 300) {
            return $result->Id;
            //} else {
            //  $this->dal->deleteSearchTerm($result->Id);
            //return null;
            //}
        } else {
            return null;
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

    public function createGABooksFromDB($books) {
        var_dump($books);
        die();
    }

    public function getDBBooks($titleId, $author, $year, $language, $isGA) {
        if($isGA) {
            $GALang = $this->changeLangValue($language);
            $results = $this->dal->getDBGABooks($titleId, $author, $year, $GALang);
            $books = $this->createGABooksFromDB($results);
        } else {
            $results = $this->dal->getDBBHLBooks($titleId, $author, $year, $language);
        }
        return $books;
    }

    //http://stackoverflow.com/questions/11330480/strip-php-variable-replace-white-spaces-with-dashes
    public function trimParam($param) {
        $param = preg_replace("/[\s-]+/", " ", $param);
        $param = preg_replace("/[\s_]/", "+", $param);
        return $param;
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

        $none = "NONE";

        if(!empty($title) && empty($author)) {
            if(!empty($year) && $language !== $none) {
                $middle = $titleQuery . $yearQuery . $langQuery;
            } elseif(empty($year) && $language === $none) {
                $middle = $titleQuery;
            } elseif(empty($year) && $language !== $none) {
                $middle = $titleQuery . $langQuery;
            } elseif(!empty($year) && $language === $none) {
                $middle = $titleQuery . $yearQuery;
            }
        } elseif(empty($title) && !empty($author)) {
            if(!empty($year) && $language !== $none) {
                $middle = $authQuery . $yearQuery . $langQuery;
            } elseif(empty($year) && $language === $none) {
                $middle = $authQuery;
            } elseif(empty($year) && $language !== $none) {
                $middle = $authQuery . $langQuery;
            } elseif(!empty($year) && $language === $none) {
                $middle = $authQuery . $yearQuery;
            }
        } elseif(!empty($title) && !empty($author)) {
            if(!empty($year) && $language !== $none) {
                $middle = $titleQuery . $authQuery . $yearQuery . $langQuery;
            } elseif(empty($year) && $language === $none) {
                $middle = $titleQuery . $authQuery;
            } elseif(empty($year) && $language !== $none) {
                $middle = $titleQuery . $authQuery . $langQuery;
            } elseif(!empty($year) && $language === $none) {
                $middle = $titleQuery . $authQuery . $yearQuery;
            }
        }
        $url = $start . $middle . $end;
        return $url;
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
                    $item->PublisherName, $item->PublicationDate, $item->Items[0]->Contributor, "ENG");
                $i = 0;
                foreach($item->Authors as $auth) {
                    if(substr($auth->Role, 0, 4) === 'Main') {
                        if($i == 0) {
                            $book->setAuthor(new BHLAuthor($auth->Name, $auth->Role));
                        } else {
                            $book->addCoAuthor(new BHLAuthor($auth->Name, $auth->Role));
                        }
                    }
                    $i++;
                }
                foreach($item->Authors as $auth) {
                    if(substr($auth->Role, 0, 4) !== 'Main') {
                        $book->addCoAuthor(new BHLAuthor($auth->Name, $auth->Role));
                    }
                }
                array_push($books, $book);
            }
            //http://stackoverflow.com/questions/4282413/sort-array-of-objects-by-object-fields
            usort($books, array($this, 'cmp'));
        }
        return $books;
    }

    public function createGABooks($items) {
        $books = array();
        if(!empty($items)) {
            foreach($items as $item) {
                $book = new GABook($item->guid, $item->edmIsShownAt[0], $item->title[0], $item->year[0], $item->dcLanguage[0]);
                $i = 0;
                foreach($item->dcCreator as $auth) {
                    if($i == 0) {
                        $book->setAuthor(new GAAuthor($auth));
                    } else {
                        $book->addCoAuthor(new GAAuthor($auth));
                    }
                    $i++;
                }
                array_push($books, $book);
            }
            usort($books, array($this, "cmp"));
        }
        return $books;
    }

    public function saveResultsinDB($title, $BHLBooks, $GABooks) {

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

}