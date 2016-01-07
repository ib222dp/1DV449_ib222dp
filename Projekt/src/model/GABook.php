<?php

class GABook {

    private $titleUrl;
    private $itemUrl;
    private $title;
    private $year;
    private $lang;
    private $author;
    private $coAuthors;

    public function __construct($titleUrl, $itemUrl, $title, $year, $lang) {
        $this->titleUrl = $titleUrl;
        $this->itemUrl = $itemUrl;
        $this->title = $title;
        $this->year = $year;
        $this->lang = $lang;
        $this->coAuthors = array();
    }

    public function getTitleUrl() {
        return $this->titleUrl;
    }

    public function getTitle() {
        return $this->title;
    }

    public function getTitleListItem() {
        return '<li><a href="' . $this->titleUrl . '" target="_blank">' . $this->title . '</a></li>';
    }

    public function getItemUrl() {
        return $this->itemUrl;
    }

    public function getUrlListItem() {
        return '<li><a href="' . $this->itemUrl . '" target="_blank">View book</a></li>';
    }

    public function getYear() {
        return $this->year;
    }

    public function getYearListItem() {
        return '<li>Year: ' . $this->year . '</li>';
    }

    public function getLanguage() {
        return $this->lang;
    }

    public function getAuthor() {
        return $this->author;
    }

    public function setAuthor($author) {
        $this->author = $author;
    }

    public function getCoAuthors() {
        return $this->coAuthors;
    }

    public function addCoAuthor($author) {
        array_push($this->coAuthors, $author);
    }

    public function getAuthorListItem() {
        if(substr($this->author->getName(), 0, 6) === "Contr:") {
            $auth = str_replace(substr($this->author->getName(), 0, 6), "", $this->author->getName());
            $list = 'Contributor: ' . $auth . ' -- ';
        } else {
            $list = 'By: ' . $this->author->getName() . ' -- ';
        }

        foreach($this->coAuthors as $author) {
            if(substr($author->getName(), 0, 6) === "Contr:") {
                $auth = str_replace(substr($author->getName(), 0, 6), "", $author->getName());
            }
            $list .= $author->getName() . ' -- ';
        }
        $list = rtrim($list, ' - ');
        return '<li>' . $list . '</li>';
    }

}