<?php

require_once("APIDAO.php");
require_once("GAUrl.php");
require_once("BHLUrl.php");
require_once("Book.php");
require_once("BHLBook.php");
require_once("GABook.php");
require_once("Author.php");
require_once("Contributor.php");

class APIModel extends MainModel
{
    private $APIDAO;
    private $GAUrl;
    private $BHLUrl;

    public function __construct() {
        $this->APIDAO = new APIDAO();
        $this->GAUrl = new GAUrl();
        $this->BHLUrl = new BHLUrl();
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

    public function getAPIResults($url, $isGA) {
        $data = $this->APIDAO->getData($url);
        if($data === null) {
            $this->setErrorMessage("Something went wrong while fetching data from the API:s.");
            return null;
        } else {
            $results = json_decode($data);
            if($isGA) {
                if($results->success === true) {
                    $items = $results->items;
                } else {
                    $this->setErrorMessage("Something went wrong while fetching data from the API:s.");
                    return null;
                }
            } else {
                if($results->Status === 'ok') {
                    $items = $results->Result;
                } else {
                    $this->setErrorMessage("Something went wrong while fetching data from the API:s.");
                    return null;
                }
            }
            if($items === null) {
                $items = array();
            }
            return $items;
        }
    }

    public function cmp($a, $b) {
        return strcmp($a->getTitle(), $b->getTitle());
    }

    public function createBooks($items, $isGA) {
        $books = array();
        if(!empty($items)) {
            foreach($items as $item) {
                if($isGA) {
                    $book = $this->createGABook($item->guid, $item->edmIsShownAt[0], $item->title[0],
                        $item->year[0], $item->dcLanguage[0]);
                    $book->setAuthContr($item->dcCreatorLangAware, $item->dcContributorLangAware);
                } else {
                    $book = $this->createBHLBook($item->TitleUrl, $item->Items[0]->ItemUrl, $item->FullTitle,
                        $item->Edition, $item->PublisherPlace, $item->PublisherName,
                        $item->PublicationDate, $item->Items[0]->Contributor, null);
                    $book->setAuthors($item->Authors);
                }
                array_push($books, $book);
            }
            //http://stackoverflow.com/questions/4282413/sort-array-of-objects-by-object-fields
            usort($books, array($this, 'cmp'));
        }
        return $books;
    }

}