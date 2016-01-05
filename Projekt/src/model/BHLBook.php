<?php

class BHLBook {

    private $titleUrl;
    private $itemUrl;
    private $title;
    private $edition;
    private $publisherPlace;
    private $publisherName;
    private $publicationDate;
    private $contributor;
    private $lang;
    private $authors;

    public function __construct($titleUrl, $itemUrl, $title, $edition, $publisherPlace, $publisherName, $publicationDate, $contributor, $lang) {
        $this->titleUrl = $titleUrl;
        $this->itemUrl = $itemUrl;
        $this->title = $title;
        $this->edition = $edition;
        $this->publisherPlace = $publisherPlace;
        $this->publisherName = $publisherName;
        $this->publicationDate = $publicationDate;
        $this->contributor = $contributor;
        $this->lang = $lang;
        $this->authors = array();
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

    public function getAuthors() {
        return $this->authors;
    }

    public function addAuthor($author) {
        array_push($this->authors, $author);
    }

    public function getAuthorListItem() {
        $list ='';
        foreach($this->authors as $author) {
            $list .= $author->getName() . ' - ';
        }
        $list = rtrim($list, ' - ');
        $list = rtrim($list, ',');
        return '<li>By: ' . $list . '</li>';
    }

    public function getEdition() {
        return $this->edition;
    }

    public function getEditionListItem() {
        return '<li>Edition: ' . $this->edition . '</li>';
    }

    public function getPublisherPlace() {
        return $this->publisherPlace;
    }

    public function getPublisherName() {
        return $this->publisherName;
    }

    public function getPublicationDate() {
        return $this->publicationDate;
    }

    public function getPubListItem() {
        return '<li>Publication info: ' . $this->publisherPlace . ' ' . $this->publisherName . ' ' . $this->publicationDate . '</li>';
    }

    public function getContributor() {
        return $this->contributor;
    }

    public function getContrListItem() {
        return '<li>Contributed by: ' . $this->contributor . '</li>';
    }

    public function getLanguage() {
        return $this->lang;
    }

}