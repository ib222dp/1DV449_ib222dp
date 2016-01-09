<?php

abstract class Book {

    public function __construct() {

    }

    public function getTitleUrl() {
        return $this->titleUrl;
    }

    public function setTitleUrl($titleUrl) {
        if($titleUrl !== null) {
            $this->titleUrl = $titleUrl;
        } else {
            $this->titleUrl = "";
        }
    }

    public function getTitle() {
        return $this->title;
    }

    public function setTitle($title) {
        if($title !== null) {
            $this->title = $title;
        } else {
            $this->title = "";
        }
    }

    public function getTitleListItem() {
        if($this->titleUrl !== '' && $this->title !== '') {
            return '<li><a href="' . $this->titleUrl . '" target="_blank">' . $this->title . '</a></li>';
        } elseif($this->titleUrl === '' && $this->title === '') {
            return '<li>No title</li>';
        } elseif($this->title === '') {
            return '<li><a href="' . $this->titleUrl . '" target="_blank">No title</a></li>';
        } else {
            return '<li>' . $this->title . '</li>';
        }
    }

    public function getItemUrl() {
        return $this->itemUrl;
    }

    public function setItemUrl($itemUrl) {
        if($itemUrl !== null) {
            $this->itemUrl = $itemUrl;
        } else {
            $this->itemUrl = "";
        }
    }

    public function getUrlListItem() {
        if($this->itemUrl === '') {
            return '<li></li>';
        } else {
            return '<li><a href="' . $this->itemUrl . '" target="_blank">View book</a></li>';
        }
    }

    public function getYear() {
        return $this->year;
    }

    public function setYear($year) {
        if($year !== null) {
            if(is_numeric(substr($year, 0, 4))) {
                $this->year = substr($year, 0, 4);
            } else {
                $this->year = "";
            }
        } else {
            $this->year = "";
        }
    }

    public function getLanguage() {
        return $this->lang;
    }

    public function setLanguage($language) {
        if($language !== null) {
            $this->language = $language;
        } else {
            $this->language = "";
        }
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