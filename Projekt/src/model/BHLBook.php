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
            return '<li>Edition: ' . $this->edition . '</li>';
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
            return '<li>Publication info: ' . $this->pubPlace . ' ' . $this->pubName . ' ' . $this->year . '</li>';
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
            return '<li>Provided by: ' . $this->provider . '</li>';
        }
    }

    public function getAuthorListItem() {
        if($this->author === 'No author' && empty($this->coAuthors)) {
            return '<li></li>';
        } else {
            if($this->author !== 'No author') {
                $list = 'Main: ' . $this->author->getName() . ' -- ';
            }
            foreach($this->coAuthors as $author) {
                $list .= 'Co: ' . $author->getName() . ' -- ';
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
                for($i = 1; $i <= count($temp); $i++) {
                    $this->addCoAuthor($temp[$i]);
                }
            } else {
                $this->addCoAuthor('No coauthors');
            }
        } else {
            $this->setAuthor(new Author('No author'));
            $this->addCoAuthor('No coauthors');
        }
    }

}