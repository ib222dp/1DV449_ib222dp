<?php

abstract class Book {

    public function __construct() {

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

}