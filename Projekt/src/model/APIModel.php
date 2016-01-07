<?php

require_once("APIDAO.php");
require_once("GAUrl.php");
require_once("BHLUrl.php");
require_once("Book.php");
require_once("BHLBook.php");
require_once("BHLAuthor.php");
require_once("GABook.php");
require_once("GAAuthor.php");

class APIModel extends BBOModel
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

    public function createBooks($items, $isGA) {
        $books = array();
        if(!empty($items)) {
            foreach($items as $item) {
                if($isGA) {
                    $book = new GABook($item->guid, $item->edmIsShownAt[0], $item->title[0], $item->year[0], $item->dcLanguage[0]);
                    if( $item->dcCreatorLangAware !== null && $item->dcContributorLangAware !== null ||
                        $item->dcCreatorLangAware !== null && $item->dcContributorLangAware == null) {
                        $i = 0;
                        foreach($item->dcCreatorLangAware->def as $auth) {
                            if($i == 0) {
                                $book->setAuthor(new GAAuthor($auth));
                            } else {
                                $book->addCoAuthor(new GAAuthor($auth));
                            }
                            $i++;
                        }
                    } elseif(($item->dcCreatorLangAware == null) && ($item->dcContributorLangAware !== null)) {
                        $i = 0;
                        foreach($item->dcContributorLangAware->def as $auth) {
                            if($i == 0) {
                                $book->setAuthor(new GAAuthor('Contr: ' . $auth));
                            } else {
                                $book->addCoAuthor(new GAAuthor('Contr: ' . $auth));
                            }
                            $i++;
                        }
                    } else {
                        $book->setAuthor(new GAAuthor('No author'));
                    }
                } else {
                    $book = new BHLBook($item->TitleUrl, $item->Items[0]->ItemUrl, $item->FullTitle, $item->Edition, $item->PublisherPlace,
                        $item->PublisherName, $item->PublicationDate, $item->Items[0]->Contributor, "ENG");
                    $authors = array();
                    foreach($item->Authors as $auth) {
                        if(substr($auth->Role, 0, 4) === 'Main') {
                            $mainAuth = new BHLAuthor($auth->Name, $auth->Role);
                            array_push($authors, $mainAuth);
                            break;
                        }
                    }
                    foreach($item->Authors as $auth) {
                        if(substr($auth->Role, 0, 4) === 'Main') {
                            $coAuth = new BHLAuthor($auth->Name, $auth->Role);
                            array_push($authors, $coAuth);
                        }
                    }
                    foreach($item->Authors as $auth) {
                        if(substr($auth->Role, 0, 4) !== 'Main') {
                            $coAuth = new BHLAuthor($auth->Name, $auth->Role);
                            array_push($authors, $coAuth);
                        }
                    }
                    $book->setAuthor($authors[0]);
                    for($i = 1; $i <= count($authors); $i++) {
                        $book->addCoAuthor($authors[$i]);
                    }

                }
                array_push($books, $book);
            }
            //http://stackoverflow.com/questions/4282413/sort-array-of-objects-by-object-fields
            //usort($books, array($this, 'cmp'));
        }
        return $books;
    }

}