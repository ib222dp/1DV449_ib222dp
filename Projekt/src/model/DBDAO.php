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
            $result = $this->dbc->query("SELECT * FROM titlesearch WHERE title='" . $escTitle . "'");
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
            $this->dbc->query("UPDATE titlesearch SET saved_date='" . $time . "' WHERE Id='" . $titleId . "'");
        } else {
            exit();
        }
    }

    public function saveSearchTerm($title) {
        $escTitle = $this->dbc->real_escape_string($title);
        $time = time();
        $query = "INSERT INTO titlesearch (`Id`, `title`, `saved_date`)
                  VALUES (NULL,'$escTitle', '$time')";
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
                if(!empty($BHLBooks)) {
                    foreach($BHLBooks as $BHLBook) {
                        $titleUrl = $BHLBook->getTitleUrl();
                        $itemUrl = $BHLBook->getItemUrl();
                        $title = $BHLBook->getTitle();
                        $edition = $BHLBook->getEdition();
                        $pubPlace = $BHLBook->getPubPlace();
                        $pubName = $BHLBook->getPubName();
                        $year = $BHLBook->getYear();
                        $provider = $BHLBook->getProvider();
                        $lang = $BHLBook->getLanguage();
                        $author = $BHLBook->getAuthor();
                        $authorName = $author->getName();
                        $coAuthors = $BHLBook->getCoAuthors();

                        $authQuery = "SELECT Id, auth_name FROM mainauthor WHERE auth_name = ?";
                        $authStmt = $this->dbc->prepare($authQuery);
                        $authStmt->bind_param("s", $authorName);
                        if($authStmt->execute() === false) {
                            throw new Exception($this->dbc->error);
                        }
                        $authStmt->store_result();
                        if($authStmt->num_rows === 1) {
                            $authStmt->bind_result($authId, $mainAuthName);
                            while($authStmt->fetch()) {
                                $authorId = $authId;
                            }
                        } else {
                            $insAuthQuery = "INSERT INTO mainauthor (Id, auth_name) VALUES (NULL, ?)";
                            $insAuthStmt = $this->dbc->prepare($insAuthQuery);
                            $insAuthStmt->bind_param("s", $authorName);
                            if($insAuthStmt->execute() === false) {
                                throw new Exception($this->dbc->error);
                            }
                            $authorId = $this->dbc->insert_id;
                        }

                        $query = "INSERT INTO bhlbook (Id, titlesearch_id, mainauthor_id,
                                                       title_url, item_url, title, edition,
                                                       pub_place, pub_name, pub_year, provider, lang)
                                                VALUES (NULL, ?, ?,
                                                       ?, ?, ?, ?,
                                                       ?, ?, ?, ?, ?)";
                        $queryStmt = $this->dbc->prepare($query);
                        $queryStmt->bind_param("iisssssssss", $titleId, $authorId,
                            $titleUrl, $itemUrl, $title, $edition,
                            $pubPlace, $pubName, $year, $provider, $lang);
                        if($queryStmt->execute() === false) {
                            throw new Exception($this->dbc->error);
                        }
                        $newBookId = $this->dbc->insert_id;

                        foreach($coAuthors as $coAuthor) {
                            $coAuthName = $coAuthor->getName();
                            $coAuthQuery = "SELECT Id, auth_name FROM coauthor WHERE auth_name = ?";
                            $coAuthStmt = $this->dbc->prepare($coAuthQuery);
                            $coAuthStmt->bind_param("s", $coAuthName);
                            if($coAuthStmt->execute() === false) {
                                throw new Exception($this->dbc->error);
                            }
                            $coAuthStmt->store_result();
                            if($coAuthStmt->num_rows() === 1) {
                                $coAuthStmt->bind_result($coAuthId, $coAuthName);
                                while($coAuthStmt->fetch()) {
                                    $coAuthorId = $coAuthId;
                                }
                            } else {
                                $insCoAuthQuery = "INSERT INTO coauthor (Id, auth_name) VALUES (NULL, ?)";
                                $insCoAuthStmt = $this->dbc->prepare($insCoAuthQuery);
                                $insCoAuthStmt->bind_param("s", $coAuthName);
                                if($insCoAuthStmt->execute() === false) {
                                    throw new Exception($this->dbc->error);
                                }
                                $coAuthorId = $this->dbc->insert_id;
                            }

                            $bookCoAuthQuery = "INSERT INTO bhlbook_coauthor (Id, bhlbook_FK, coauthor_FK)
                                                                      VALUES (NULL, ?, ?)";
                            $bookCoAuthStmt = $this->dbc->prepare($bookCoAuthQuery);
                            $bookCoAuthStmt->bind_param("ii", $newBookId, $coAuthorId);
                            if($bookCoAuthStmt->execute() === false) {
                                throw new Exception($this->dbc->error);
                            }
                        }
                    }
                }
                if(!empty($GABooks)) {
                    foreach($GABooks as $GABook) {
                        $titleUrl = $GABook->getTitleUrl();
                        $itemUrl = $GABook->getItemUrl();
                        $title = $GABook->getTitle();
                        $year = $GABook->getYear();
                        $lang = $GABook->getLanguage();
                        $author = $GABook->getAuthor();
                        $authorName = $author->getName();
                        $contributor = $GABook->getContributor();
                        $contrName = $contributor->getName();
                        $coAuthors = $GABook->getCoAuthors();

                        $authQuery = "SELECT Id, auth_name FROM mainauthor WHERE auth_name = ?";
                        $authStmt = $this->dbc->prepare($authQuery);
                        $authStmt->bind_param("s", $authorName);
                        if($authStmt->execute() === false) {
                            throw new Exception($this->dbc->error);
                        }
                        $authStmt->store_result();
                        if($authStmt->num_rows === 1) {
                            $authStmt->bind_result($authId, $mainAuthName);
                            while($authStmt->fetch()) {
                                $authorId = $authId;
                            }
                        } else {
                            $insAuthQuery = "INSERT INTO mainauthor (Id, auth_name) VALUES (NULL, ?)";
                            $insAuthStmt = $this->dbc->prepare($insAuthQuery);
                            $insAuthStmt->bind_param("s", $authorName);
                            if($insAuthStmt->execute() === false) {
                                throw new Exception($this->dbc->error);
                            }
                            $authorId = $this->dbc->insert_id;
                        }

                        $contrQuery = "SELECT Id, name FROM contributor WHERE name = ?";
                        $contrStmt = $this->dbc->prepare($contrQuery);
                        $contrStmt->bind_param("s", $contrName);
                        if($contrStmt->execute() === false) {
                            throw new Exception($this->dbc->error);
                        }
                        $contrStmt->store_result();
                        if($contrStmt->num_rows === 1) {
                            $contrStmt->bind_result($conId, $contrName);
                            while($contrStmt->fetch()) {
                                $contrId = $conId;
                            }
                        } else {
                            $insContrQuery = "INSERT INTO contributor (Id, name) VALUES (NULL, ?)";
                            $insContrStmt = $this->dbc->prepare($insContrQuery);
                            $insContrStmt->bind_param("s", $contrName);
                            if($insContrStmt->execute() === false) {
                                throw new Exception($this->dbc->error);
                            }
                            $contrId = $this->dbc->insert_id;
                        }

                        $query = "INSERT INTO gabook (Id, titlesearch_id, mainauthor_id,
                                                      contributor_id, title_url, item_url,
                                                      title, pub_year, lang)
                                              VALUES (NULL, ?, ?,
                                                      ?, ?, ?,
                                                      ?, ?, ?)";
                        $queryStmt = $this->dbc->prepare($query);
                        $queryStmt->bind_param("iiisssss", $titleId, $authorId,
                            $contrId, $titleUrl, $itemUrl,
                            $title, $year, $lang);
                        if($queryStmt->execute() === false) {
                            throw new Exception($this->dbc->error);
                        }
                        $newBookId = $this->dbc->insert_id;

                        foreach($coAuthors as $coAuthor) {
                            $coAuthName = $coAuthor->getName();
                            $coAuthQuery = "SELECT Id, auth_name FROM coauthor WHERE auth_name = ?";
                            $coAuthStmt = $this->dbc->prepare($coAuthQuery);
                            $coAuthStmt->bind_param("s", $coAuthName);
                            if($coAuthStmt->execute() === false) {
                                throw new Exception($this->dbc->error);
                            }
                            $coAuthStmt->store_result();
                            if($coAuthStmt->num_rows() === 1) {
                                $coAuthStmt->bind_result($coAuthId, $coAuthName);
                                while($coAuthStmt->fetch()) {
                                    $coAuthorId = $coAuthId;
                                }
                            } else {
                                $insCoAuthQuery = "INSERT INTO coauthor (Id, auth_name) VALUES (NULL, ?)";
                                $insCoAuthStmt = $this->dbc->prepare($insCoAuthQuery);
                                $insCoAuthStmt->bind_param("s", $coAuthName);
                                if($insCoAuthStmt->execute() === false) {
                                    throw new Exception($this->dbc->error);
                                }
                                $coAuthorId = $this->dbc->insert_id;
                            }

                            $bookCoAuthQuery = "INSERT INTO gabook_coauthor (Id, gabook_FK, coauthor_FK)
                                                                     VALUES (NULL, ?, ?)";
                            $bookCoAuthStmt = $this->dbc->prepare($bookCoAuthQuery);
                            $bookCoAuthStmt->bind_param("ii", $newBookId, $coAuthorId);
                            if($bookCoAuthStmt->execute() === false) {
                                throw new Exception($this->dbc->error);
                            }
                        }
                    }
                }
                $this->dbc->commit();
                $this->dbc->autocommit(true);
                return true;
            } catch(Exception $e) {
                $this->dbc->rollback();
                $this->dbc->autocommit(true);
                return false;
            }
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
                $this->dbc->autocommit(true);
                return true;
            } catch(Exception $e) {
                $this->dbc->rollback();
                $this->dbc->autocommit(true);
                return false;
            }
        } else {
            exit();
        }
    }

}