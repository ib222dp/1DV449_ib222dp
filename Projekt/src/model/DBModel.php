<?php

require_once("DBDAO.php");
require_once("Book.php");
require_once("BHLBook.php");
require_once("GABook.php");
require_once("Author.php");
require_once("Contributor.php");

class DBModel extends MainModel
{
    private $DBDAO;

    public function __construct() {
        $this->DBDAO = new DBDAO();
    }

    public function getSearchTerm($title) {
        $result = $this->DBDAO->getSearchTerm($title);
        if($result !== null) {
            if((time() - (int)$result->saved_date) <= 300) {
                return array($result, true);
            } else {
                return array($result, false);
            }
        } else {
            return null;
        }
    }

    private function createBooks($items, $isGA) {
        $books = array();
        foreach($items as $item) {
            if($isGA) {
                $book = $this->createGABook($item[1], $item[2], $item[3], $item[4], $item[5]);
                $book->setAuthor(new Author($item[6]));
                $book->setContributor(new Contributor($item[7]));
                $coAuthors = explode('*', $item[8]);
            } else {
                $book = $this->createBHLBook($item[1], $item[2], $item[3], $item[4], $item[5],
                    $item[6], $item[7], $item[8], $item[9]);
                $book->setAuthor(new Author($item[10]));
                $coAuthors = explode('*', $item[11]);
            }
            foreach($coAuthors as $coAuthor) {
                $book->addCoAuthor(new Author($coAuthor));
            }
            array_push($books, $book);
        }
        return $books;
    }

    public function getBooks($titleId, $author, $year, $language, $isGA) {
        if($isGA) {
            $GALang = $this->changeLangValue($language);
            $items = $this->DBDAO->getBooks($titleId, $author, $year, $GALang, $isGA);
        } else {
            $language = 'NONE';
            $items = $this->DBDAO->getBooks($titleId, $author, $year, $language, $isGA);
        }
        $books = $this->createBooks($items, $isGA);
        return $books;
    }

    public function saveResults($title, $BHLBooks, $GABooks, $isNewTitle) {
        $this->DBDAO->saveSearchResults($title, $BHLBooks, $GABooks, $isNewTitle);
    }

    public function deleteSearchTerm($titleId) {
        $this->DBDAO->deleteSearchTerm($titleId);
    }

}