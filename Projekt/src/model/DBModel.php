<?php

require_once("DBDAO.php");
require_once("Book.php");
require_once("BHLBook.php");
require_once("BHLAuthor.php");
require_once("GABook.php");
require_once("GAAuthor.php");

class DBModel extends BBOModel
{
    private $DBDAO;

    public function __construct() {
        $this->DBDAO = new DBDAO();
    }

    public function getSearchTerm($title) {
        $result = $this->DBDAO->getSearchTerm($title);
        if($result !== null) {
            //if(($result->saved_date - time()) <= 300) {
            return $result->Id;
            //} else {
            //delete child results, update saveddate
            //  $this->DBDAO->deleteSearchTerm($result->Id);
            //return null;
            //}
        } else {
            return null;
        }
    }

    private function createBooks($items, $isGA) {
        $books = array();
        foreach($items as $item) {
            if($isGA) {
                $book = new GABook($item[1], $item[2], $item[3], $item[4], $item[5]);
                $book->setAuthor(new GAAuthor($item[6]));
                $coAuthors = explode('*', $item[7]);
                foreach($coAuthors as $coAuthor) {
                    $book->addCoAuthor(new GAAuthor($coAuthor));
                }
            } else {
                $book = new BHLBook($item[1], $item[2], $item[3], $item[4], $item[5], $item[6], $item[7],
                    $item[8], $item[9]);
                $book->setAuthor(new BHLAuthor($item[10], "Role - Main Entry"));
                $coAuthors = explode('*', $item[11]);
                foreach($coAuthors as $coAuthor) {
                    $book->addCoAuthor(new BHLAuthor($coAuthor, "Role - Added Entry"));
                }
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
            $items = $this->DBDAO->getBooks($titleId, $author, $year, $language, $isGA);
        }
        $books = $this->createBooks($items, $isGA);
        return $books;
    }

    public function saveResults($title, $BHLBooks, $GABooks) {

    }

}