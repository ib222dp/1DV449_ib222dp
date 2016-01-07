<?php

class GABook extends Book {

    protected $titleUrl;
    protected $itemUrl;
    protected $title;
    private $year;
    protected $lang;
    protected $author;
    protected $coAuthors;

    public function __construct($titleUrl, $itemUrl, $title, $year, $lang) {
        $this->titleUrl = $titleUrl;
        $this->itemUrl = $itemUrl;
        $this->title = $title;
        $this->year = $year;
        $this->lang = $lang;
        $this->coAuthors = array();
    }

    public function getYear() {
        return $this->year;
    }

    public function getYearListItem() {
        return '<li>Year: ' . $this->year . '</li>';
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