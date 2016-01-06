<?php

require_once("DBKey.php");

class DAL {
    private $dbc;
    private $DBKey;

    public function __construct() {
        $this->DBKey = new DBKey();
        $this->dbc = new mysqli($this->DBKey->getHost(), $this->DBKey->getUsername(), $this->DBKey->getPassword(), $this->DBKey->getDBName());
    }

    public function startCURL($url) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        return $ch;
    }

    public function getData($url) {
        $ch = $this->startCURL($url);
        $data = curl_exec($ch);
        if($data == false) {
            curl_close($ch);
            die('<a href="index.php">Tillbaka</a><p>Något gick fel.</p>');
        } else {
            curl_close($ch);
            return $data;
        }
    }

    public function searchTermInDB($title) {
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

    public function getDBBHLBooks($titleId, $author, $year, $language) {

    }

    public function getDBGABooks($titleId, $author, $year, $language) {
        /*if(!empty($author)) {
            $escAuth = $this->dbc->real_escape_string($author);
        }
        if(!empty($year)) {
            $escYear = $this->dbc->real_escape_string($year);
        }
        if(!empty($language)) {
            $escLang = $this->dbc->real_escape_string($language);
        }*/

        $query =    'SELECT gb.title_url, gb.item_url, gb.title, gb.pub_year, gma.auth_name, GROUP_CONCAT(gca.auth_name, SEPARATOR "*")
                    FROM gabook AS gb
                    LEFT JOIN ga_mainauthor AS gma ON gb.mainauthor_id = gma.Id
                    LEFT OUTER JOIN gabook_gacoauthor AS gbgca ON gbgca.gabook_FK = gb.Id
                    LEFT OUTER JOIN ga_coauthor AS gca ON gbgca.gacoauthor_FK = gca.Id
                    WHERE gb.titlesearch_id="' . $titleId . '"
                    GROUP BY ';


        if(mysqli_set_charset($this->dbc, 'utf8')) {
            if($result = $this->dbc->query($query)){
                $books = mysqli_fetch_all($result);
                var_dump($books);
                die();
            } else {
                echo "wrong";
                die();
            }
            return $result;
        } else {
            exit();
        }
    }

}