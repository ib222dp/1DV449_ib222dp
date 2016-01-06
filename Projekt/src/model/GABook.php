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
        if($this->author !== null) {
            $list = $this->author->getName();
        }
        foreach($this->coAuthors as $author) {
            $list .= $author->getName() . ' - ';
        }
        $list = rtrim($list, ' - ');
        return '<li>By: ' . $list . '</li>';
    }

}