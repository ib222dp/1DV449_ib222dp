<?php

class GABook extends Book {

    protected $titleUrl;
    protected $itemUrl;
    protected $title;
    protected $year;
    protected $lang;
    protected $author;
    private $contributor;
    protected $coAuthors;

    public function __construct() {
        $this->coAuthors = array();
    }

    public function setAuthContr($author, $contributor) {
        if($author !== null && $author->def !== null && !empty($author->def)) {
            $i = 0;
            foreach($author->def as $auth) {
                if($i == 0) {
                    $this->setAuthor(new Author($auth));
                    $this->setContributor(new Contributor('No contributor'));
                } else {
                    $this->addCoAuthor(new Author($auth));
                }
                $i++;
            }
            if(empty($this->coAuthors)) {
                $this->addCoAuthor(new Author('No coauthors'));
            }
        } elseif(($author === null) && ($contributor !== null && $contributor->def !== null && !empty($contributor->def))) {
            $i = 0;
            foreach($contributor->def as $contr) {
                if($i == 0) {
                    $this->setContributor(new Contributor($contr));
                    $this->setAuthor(new Author('No author'));
                } else {
                    $this->addCoAuthor(new Author($contr));
                }
                $i++;
            }
            if(empty($this->coAuthors)) {
                $this->addCoAuthor(new Author('No coauthors'));
            }
        } else {
            $this->setAuthor(new Author('No author'));
            $this->setContributor(new Contributor('No contributor'));
            $this->addCoAuthor(new Author('No coauthors'));
        }
    }

    public function getContributor() {
        return $this->contributor;
    }

    public function setContributor($contributor) {
        $this->contributor = $contributor;
    }

    public function getYearListItem() {
        if($this->year === "") {
            return '<li></li>';
        } else {
            return '<li>Year: ' . htmlspecialchars($this->year, ENT_QUOTES) . '</li>';
        }
    }

    public function getAuthorListItem() {
        if($this->author->getName() !== 'No author') {
            $list = 'By: ' . htmlspecialchars($this->author->getName(), ENT_QUOTES) . ' -- ';
        } elseif($this->contributor->getName() !== 'No contributor') {
            $list = 'Contributor: ' . htmlspecialchars($this->contributor->getName(), ENT_QUOTES) . ' -- ';
        }
        if($this->coAuthors[0]->getName() !== "No coauthors") {
            foreach($this->coAuthors as $author) {
                $list .= htmlspecialchars($author->getName(), ENT_QUOTES) . ' -- ';
            }
        }
        $list = rtrim($list, ' -- ');
        return '<li>' . $list . '</li>';
    }

}