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

    public function searchTermInDB($title, $author, $year, $language) {
        if(!empty($title)) {
            $escTitle = $this->dbc->real_escape_string($title);
        }
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

        if(!empty($title) && empty($author)) {
            if(!empty($year) && $language !== $none) {
                $query = 'SELECT * FROM searchterm WHERE title="' . $escTitle . '"
				          AND pub_year="' . $escYear .  '" AND lang="' . $escLang . '"';
            } elseif(empty($year) && $language === $none) {
                $query = 'SELECT * FROM searchterm WHERE title="' . $escTitle . '"';
            } elseif(empty($year) && $language !== $none) {
                $query = 'SELECT * FROM searchterm WHERE title="' . $escTitle . '"
				          AND lang="' . $escLang . '"';
            } elseif(!empty($year) && $language === $none) {
                $query = 'SELECT * FROM searchterm WHERE title="' . $escTitle . '"
				          AND pub_year="' . $escYear . '"';
            }
        } elseif(empty($title) && !empty($author)) {
            if(!empty($year) && $language !== $none) {
                $query = 'SELECT * FROM searchterm WHERE author ="' . $escAuth . '"
				          AND pub_year="' . $escYear .  '" AND lang="' . $escLang . '"';
            } elseif(empty($year) && $language === $none) {
                $query = 'SELECT * FROM searchterm WHERE author ="' . $escAuth . '"';
            } elseif(empty($year) && $language !== $none) {
                $query = 'SELECT * FROM searchterm WHERE author ="' . $escAuth . '"
				          AND lang="' . $escLang . '"';
            } elseif(!empty($year) && $language === $none) {
                $query = 'SELECT * FROM searchterm WHERE author ="' . $escAuth . '"
				          AND pub_year="' . $escYear . '"';
            }
        } elseif(!empty($title) && !empty($author)) {
            if(!empty($year) && $language !== $none) {
                $query = 'SELECT * FROM searchterm WHERE title="' . $escTitle . '"
				          AND author="' . $escAuth . '" AND pub_year="' . $escYear .  '" AND lang="' . $escLang . '"';
            } elseif(empty($year) && $language === $none) {
                $query = 'SELECT * FROM searchterm WHERE title="' . $escTitle . '"
				          AND author="' . $escAuth . '"';
            } elseif(empty($year) && $language !== $none) {
                $query = 'SELECT * FROM searchterm WHERE title="' . $escTitle . '"
				          AND author="' . $escAuth . '" AND lang="' . $escLang . '"';
            } elseif(!empty($year) && $language === $none) {
                $query = 'SELECT * FROM searchterm WHERE title="' . $escTitle . '"
				          AND author="' . $escAuth . '" AND pub_year="' . $escYear . '"';
            }
        }

        if(mysqli_set_charset($this->dbc, 'utf8')) {
            $result = $this->dbc->query($query);
            if(mysqli_num_rows($result) === 1) {
                return $result;
            } else {
                return null;
            }
        } else {
            exit();
        }
    }

    public function saveSearchTerm($title, $author, $year, $language) {

    }

}

