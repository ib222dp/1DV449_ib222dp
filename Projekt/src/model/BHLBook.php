<?php

class BHLBook extends Book {

    protected $titleUrl;
    protected $itemUrl;
    protected $title;
    private $edition;
    private $pubPlace;
    private $pubName;
    protected $year;
    private $provider;
    protected $lang;
    protected $author;
    protected $coAuthors;

    public function __construct() {
        $this->coAuthors = array();
    }

    public function getEdition() {
        return $this->edition;
    }

    public function setEdition($edition) {
        if($edition !== null) {
            $this->edition = $edition;
        } else {
            $edition = "";
        }
    }

    public function getEditionListItem() {
        if($this->edition === '') {
            return '<li></li>';
        } else {
            return '<li>Edition: ' . htmlspecialchars($this->edition, ENT_QUOTES) . '</li>';
        }
    }

    public function getPubPlace() {
        return $this->pubPlace;
    }

    public function setPubPlace($pubPlace) {
        if($pubPlace !== null) {
            $this->pubPlace = $pubPlace;
        } else {
            $this->pubPlace = "";
        }
    }

    public function getPubName() {
        return $this->pubName;
    }

    public function setPubName($pubName) {
        if($pubName !== null){
            $this->pubName = $pubName;
        } else {
            $this->pubName = "";
        }
    }

    public function getPubListItem() {
        if($this->pubPlace === '' && $this->pubName === '' && $this->year === '') {
            return '<li></li>';
        } else {
            return '<li>Publication info: ' . htmlspecialchars($this->pubPlace, ENT_QUOTES) . ' ' .
            htmlspecialchars($this->pubName, ENT_QUOTES) . ' ' . htmlspecialchars($this->year, ENT_QUOTES) . '</li>';
        }
    }

    public function getProvider() {
        return $this->provider;
    }

    public function setProvider($provider) {
        if($provider !== null) {
            $this->provider = $provider;
        } else {
            $this->provider = "";
        }
    }

    public function getProvListItem() {
        if($this->provider === '') {
            return '<li></li>';
        } else {
            return '<li>Provided by: ' . htmlspecialchars($this->provider, ENT_QUOTES) . '</li>';
        }
    }

    public function getAuthorListItem() {
        if($this->author->getName() === 'No author' && $this->coAuthors[0]->getName() === 'No coauthors') {
            return '<li></li>';
        } else {
            if($this->author->getName() !== 'No author') {
                $list = htmlspecialchars($this->author->getName(), ENT_QUOTES) . ' -- ';
            }

            foreach($this->coAuthors as $author) {
                if($author->getName() === 'No coauthors') {
                    break;
                } else {
                    $list .= htmlspecialchars($author->getName(), ENT_QUOTES) . ' -- ';
                }
            }
            $list = rtrim($list, ' -- ');
            $list = rtrim($list, ',');
            return '<li>By: ' . $list . '</li>';
        }
    }

    public function setAuthors($authors) {
        if($authors !== null && !empty($authors)) {
            $temp = array();
            foreach($authors as $auth) {
                if($auth->Role !== null && $auth->Name !== null) {
                    if(substr($auth->Role, 0, 4) === 'Main') {
                        $this->setAuthor(new Author($auth->Name));
                        break;
                    }
                }
            }
            if(!isset($this->author)) {
                $this->setAuthor(new Author('No author'));
            }
            foreach($authors as $auth) {
                if($auth->Role !== null && $auth->Name !== null) {
                    if(substr($auth->Role, 0, 4) === 'Main') {
                        $coAuth = new Author($auth->Name);
                        array_push($temp, $coAuth);
                    }
                }
            }
            if(!empty($temp)) {
                array_shift($temp);
            }
            foreach($authors as $auth) {
                if($auth->Role !== null && $auth->Name !== null) {
                    if(substr($auth->Role, 0, 4) !== 'Main') {
                        $coAuth = new Author($auth->Name);
                        array_push($temp, $coAuth);
                    }
                }
            }
            if(!empty($temp)) {
                $arrLength = count($temp);
                for($i = 0; $i < $arrLength; $i++) {
                    $this->addCoAuthor($temp[$i]);

                }
            } else {
                $this->addCoAuthor(new Author('No coauthors'));
            }
        } else {
            $this->setAuthor(new Author('No author'));
            $this->addCoAuthor(new Author('No coauthors'));
        }
    }

}