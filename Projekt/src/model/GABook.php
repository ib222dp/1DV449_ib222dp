<?php

class GABook {

    private $titleUrl;
    private $itemUrl;
    private $title;
    private $year;
    private $author;

    public function __construct($titleUrl, $itemUrl, $title, $year, $author) {
        $this->titleUrl = $titleUrl;
        $this->itemUrl = $itemUrl;
        $this->title = $title;
        $this->year = $year;
        $this->author = $author;
    }

    public function getTitleUrl() {
        return $this->titleUrl;
    }

    public function getItemUrl() {
        return $this->itemUrl;
    }

    public function getUrlListItem() {
        return '<li><a href="' . $this->itemUrl . '" target="_blank">View book</a></li>';
    }

    public function getTitle() {
        return $this->title;
    }

    public function getTitleListItem() {
        return '<li><a href="' . $this->titleUrl . '" target="_blank">' . $this->title . '</a></li>';
    }

    public function getYear() {
        return $this->year;
    }

    public function getYearListItem() {
        return '<li>Year: ' . $this->year . '</li>';
    }

    public function getAuthor() {
        return $this->author;
    }

    public function getAuthorListItem() {
        return '<li>By: ' . $this->author . '</li>';
    }

}