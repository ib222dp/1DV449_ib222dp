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
                            con.name, GROUP_CONCAT(ca.auth_name SEPARATOR "*")
                            FROM gabook AS book
                            LEFT JOIN mainauthor AS ma ON book.mainauthor_id = ma.Id
                            LEFT JOIN contributor AS con ON book.contributor_id = con.Id
                            LEFT OUTER JOIN gabook_coauthor AS bookca ON bookca.gabook_FK = book.Id
                            LEFT OUTER JOIN coauthor AS ca ON bookca.coauthor_FK = ca.Id
                            WHERE book.titlesearch_id="' . $titleId . '"';
            $groupBy =      ' GROUP BY bookca.gabook_FK';
        } else {
            $startQuery =   'SELECT book.Id, book.title_url, book.item_url, book.title, book.edition, book.pub_place,
                            book.pub_name, book.pub_year, book.provider, book.lang, ma.auth_name,
                            GROUP_CONCAT(ca.auth_name SEPARATOR "*")
                            FROM bhlbook AS book
                            LEFT JOIN mainauthor AS ma ON book.mainauthor_id = ma.Id
                            LEFT OUTER JOIN bhlbook_coauthor AS bookca ON bookca.bhlbook_FK = book.Id
                            LEFT OUTER JOIN coauthor AS ca ON bookca.coauthor_FK = ca.Id
                            WHERE book.titlesearch_id="' . $titleId . '"';
            $groupBy =      ' GROUP BY bookca.bhlbook_FK';
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
                $query = $startQuery . ' AND ma.auth_name LIKE "' . $escAuth . '%" AND book.pub_year="' . $escYear .
                    '" AND book.lang="' . $escLang . '"' . $groupBy;
            } elseif(empty($year) && $language === $none) {
                $query = $startQuery . ' AND ma.auth_name LIKE "' . $escAuth . '%"' . $groupBy;
            } elseif(empty($year) && $language !== $none) {
                $query = $startQuery . ' AND ma.auth_name LIKE "' . $escAuth . '%" AND book.lang="' . $escLang . '"' . $groupBy;
            } elseif(!empty($year) && $language === $none) {
                $query = $startQuery . ' AND ma.auth_name LIKE "' . $escAuth . '%" AND book.pub_year="' . $escYear . '"' . $groupBy;
            }
        }

        if(mysqli_set_charset($this->dbc, 'utf8')) {
            if($result = $this->dbc->query($query)){
                $books = mysqli_fetch_all($result);
                return $books;
            } else {
                die('<a href="index.php">Tillbaka</a><p>Något gick fel.</p>');
            }
        } else {
            exit();
        }
    }

    public function updateSearchTerm($titleId) {
        if(mysqli_set_charset($this->dbc, "utf8")) {
            $time = time();
            $this->dbc->query('UPDATE titlesearch SET saved_date="' . $time . '" WHERE Id="' . $titleId . '"');
        } else {
            exit();
        }
    }

    public function saveSearchTerm($title) {
        $escTitle = $this->dbc->real_escape_string($title);
        $time = time();
        $query = 'INSERT INTO titlesearch (`Id`, `title`, `saved_date`)
                  VALUES (NULL, "$escTitle", "$time")';
        if(mysqli_set_charset($this->dbc, "utf8")) {
            if($result = $this->dbc->query($query)) {
                return $this->dbc->insert_id;
            } else {
                return null;
            }
        } else {
            exit();
        }
    }

    public function saveSearchResults($titleId, $BHLBooks, $GABooks) {
        if(mysqli_set_charset($this->dbc, "utf8")) {
            try {
                $this->dbc->autocommit(false);
                foreach($BHLBooks as $BHLBook) {
                    $titleUrl = $BHLBook->getTitleUrl();
                    $itemUrl = $BHLBook->getItemUrl();
                    $title = $BHLBook->getTitle();
                    $edition = $BHLBook->getEdition();
                    $pubPlace = $BHLBook->getPubPlace();
                    $pubName = $BHLBook->getPubName();
                    $year = $BHLBook->getYear();
                    $provider = $BHLBook->getProvider();
                    $lang = $BHLBook->getLang();
                    $author = $BHLBook->getAuthor();
                    $authorName = $author->getName();
                    $coAuthors = $BHLBook->getCoAuthors();
                    $authQuery = ("SELECT * FROM author WHERE auth_name='" . $authorName . "'");
                    if(mysqli_num_rows($authQuery) === 1) {
                        $fetchedAuthor = mysqli_fetch_object($authQuery);
                        $authorId = $fetchedAuthor->Id;
                    } else {
                        $insAuthQuery = ("INSERT INTO author (`Id`, `auth_name`) VALUES (NULL, '$authorName')");
                        $authResult = $this->dbc->query($insAuthQuery);
                        if($authResult === false) {
                            throw new Exception();
                        }
                        $authorId = $this->dbc->insert_id;
                    }
                    $query = ("INSERT INTO bhlbook (`Id`, `titlesearch_id`, `mainauthor_id`,
                                                    `title_url`, `item_url`, `title`, `edition`,
                                                    `pub_place`, `pub_name`, `pub_year`, `provider`, `lang`)
                              VALUES(NULL, '$titleId', '$authorId', '$titleUrl', '$itemUrl', '$title', '$edition',
                                     '$pubPlace', '$pubName', '$year', '$provider', '$lang')");
                    $result = $this->dbc->query($query);
                    if($result === false) {
                        throw new Exception();
                    }
                }
                foreach($GABooks as $GABook) {
                    $titleUrl = $GABook->getTitleUrl();
                    $itemUrl = $GABook->getItemUrl();
                    $title = $GABook->getTitle();
                    $year = $GABook->getYear();
                    $lang = $GABook->getLang();
                    $author = $GABook->getAuthor();
                    $authorName = $author->getName();
                    $contributor = $GABook->getContributor();
                    $contrName = $contributor->getName();
                    $coAuthors = $GABook->getCoAuthors();
                    $authQuery = ("SELECT * FROM author WHERE auth_name='" . $authorName . "'");
                    if(mysqli_num_rows($authQuery) === 1) {
                        $fetchedAuthor = mysqli_fetch_object($authQuery);
                        $authorId = $fetchedAuthor->Id;
                    } else {
                        $insAuthQuery = ("INSERT INTO author (`Id`, `auth_name`) VALUES (NULL, '$authorName')");
                        $authResult = $this->dbc->query($insAuthQuery);
                        if($authResult === false) {
                            throw new Exception();
                        }
                        $authorId = $this->dbc->insert_id;
                    }
                    $contrQuery = ("SELECT * FROM contributor WHERE name='" . $contrName . "'");
                    if(mysqli_num_rows($contrQuery) === 1) {
                        $fetchedContr = mysqli_fetch_object($contrQuery);
                        $contrId = $fetchedContr->Id;
                    } else {
                        $insContrQuery = ("INSERT INTO contributor (`Id`, `name`) VALUES (NULL, '$contrName')");
                        $contrResult = $this->dbc->query($insContrQuery);
                        if($contrResult === false) {
                            throw new Exception();
                        }
                        $contrId = $this->dbc->insert_id;
                    }
                    $query = ("INSERT INTO gabook (`Id`, `titlesearch_id`, `mainauthor_id`, `contributor_id`,
                                                   `title_url`, `item_url`, `title`, `pub_year`, `lang`)
                              VALUES(NULL, '$titleId', '$authorId', '$contrId',
                                    '$titleUrl', '$itemUrl', '$title', '$year', '$lang')");
                    $result = $this->dbc->query($query);
                    if($result === false) {
                        throw new Exception();
                    }
                }
                $this->dbc->commit();
            } catch(Exception $e) {
                $this->dbc->rollback();
                return false;
            }
            $this->dbc->autocommit(true);
            return true;
        } else {
            exit();
        }
    }

    public function deleteSearchTerm($titleId) {
        if(mysqli_set_charset($this->dbc, "utf8")) {
            $this->dbc->query("DELETE FROM titlesearch WHERE Id='" . $titleId . "'");
        } else {
            exit();
        }
    }

    //http://www.pontikis.net/blog/how-to-use-php-improved-mysqli-extension-and-why-you-should
    public function deleteSearchResults($titleId) {
        if(mysqli_set_charset($this->dbc, "utf8")) {
            try {
                $this->dbc->autocommit(false);
                $result = $this->dbc->query("DELETE FROM bhlbook WHERE titlesearch_id='" . $titleId . "'");
                $result2 = $this->dbc->query("DELETE FROM gabook WHERE titlesearch_id='" . $titleId . "'");
                if($result === false || $result2 === false) {
                    throw new Exception();
                }
                $this->dbc->commit();
            } catch(Exception $e) {
                $this->dbc->rollback();
                return false;
            }
            $this->dbc->autocommit(true);
            return true;
        } else {
            exit();
        }
    }

}