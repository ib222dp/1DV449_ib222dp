<?php

require_once("DBKey.php");

class DBDAO {
    private $dbc;
    private $DBKey;

    public function __construct() {
        $this->DBKey = new DBKey();
        $this->dbc = new mysqli($this->DBKey->getHost(), $this->DBKey->getUsername(),
            $this->DBKey->getPassword(), $this->DBKey->getDBName());
    }

    public function getSearchTerm($title) {
        $escTitle = $this->dbc->real_escape_string($title);
        if(mysqli_set_charset($this->dbc, 'utf8')) {
            $result = $this->dbc->query('SELECT * FROM titlesearch WHERE title="' . $escTitle . '"');
            if(mysqli_num_rows($result) === 1) {
                $searchTerm = mysqli_fetch_object($result);
                return $searchTerm;
            } else {
                return null;
            }
        } else {
            exit();
        }
    }

    public function deleteSearchTerm($id) {
        if(mysqli_set_charset($this->dbc, "utf8")) {
            $this->dbc->query("DELETE FROM titlesearch WHERE Id='" . $id . "'");
        } else {
            exit();
        }
    }

    public function getBooks($titleId, $author, $year, $language, $isGA) {
        if(!empty($author)) {
            $escAuth = $this->dbc->real_escape_string($author);
        }
        if(!empty($year)) {
            $escYear = $this->dbc->real_escape_string($year);
        }
        if(!empty($language)) {
            $escLang = $this->dbc->real_escape_string($language);
        }
        $none = "NONE";

        if($isGA) {
            $startQuery =   'SELECT book.Id, book.title_url, book.item_url, book.title, book.pub_year, book.lang, ma.auth_name,
                            GROUP_CONCAT(gca.auth_name SEPARATOR "*")
                            FROM gabook AS book
                            LEFT JOIN ga_mainauthor AS ma ON book.mainauthor_id = ma.Id
                            LEFT OUTER JOIN gabook_gacoauthor AS gbgca ON gbgca.gabook_FK = book.Id
                            LEFT OUTER JOIN ga_coauthor AS gca ON gbgca.gacoauthor_FK = gca.Id
                            WHERE book.titlesearch_id="' . $titleId . '"';
            $groupBy =      ' GROUP BY gbgca.gabook_FK';
        } else {
            $startQuery =   'SELECT book.Id, book.title_url, book.item_url, book.title, book.edition, book.publisher_place,
                            book.publisher_name, book.publication_date, book.provider, book.lang, ma.auth_name,
                            GROUP_CONCAT(bca.auth_name SEPARATOR "*")
                            FROM bhlbook AS book
                            LEFT JOIN bhl_mainauthor AS ma ON book.mainauthor_id = ma.Id
                            LEFT OUTER JOIN bhlbook_bhlcoauthor AS bbbca ON bbbca.bhlbook_FK = book.Id
                            LEFT OUTER JOIN bhl_coauthor AS bca ON bbbca.bhlcoauthor_FK = bca.Id
                            WHERE book.titlesearch_id="' . $titleId . '"';
            $groupBy =      ' GROUP BY bbbca.bhlbook_FK';
        }

        if(empty($author)) {
            if(!empty($year) && $language !== $none) {
                $query = $startQuery . ' AND book.pub_year="' . $escYear . '" AND book.lang="' . $escLang . '"' . $groupBy;
            } elseif(empty($year) && $language === $none) {
                $query = $startQuery . $groupBy;
            } elseif(empty($year) && $language !== $none) {
                $query = $startQuery . ' AND book.lang="' . $escLang . '"' . $groupBy;
            } elseif(!empty($year) && $language === $none) {
                $query = $startQuery . ' AND book.pub_year="' . $escYear . '"' . $groupBy;
            }
        } elseif(!empty($author)) {
            if(!empty($year) && $language !== $none) {
                $query = $startQuery . ' AND ma.auth_name="' . $escAuth . '" AND book.pub_year="' . $escYear .
                    '" AND book.lang="' . $escLang . '"' . $groupBy;
            } elseif(empty($year) && $language === $none) {
                $query = $startQuery . ' AND ma.auth_name="' . $escAuth . '"' . $groupBy;
            } elseif(empty($year) && $language !== $none) {
                $query = $startQuery . ' AND ma.auth_name="' . $escAuth . '" AND book.lang="' . $escLang . '"' . $groupBy;
            } elseif(!empty($year) && $language === $none) {
                $query = $startQuery . ' AND ma.auth_name="' . $escAuth . '" AND book.pub_year="' . $escYear . '"' . $groupBy;
            }
        }

        if(mysqli_set_charset($this->dbc, 'utf8')) {
            if($result = $this->dbc->query($query)){
                $books = mysqli_fetch_all($result);
                return $books;
            } else {
                echo "Fel";
                die();
            }
            return null;
        } else {
            exit();
        }
    }

}