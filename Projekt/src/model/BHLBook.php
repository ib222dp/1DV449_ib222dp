<?php

class BHLBook extends Book {

    protected $titleUrl;
    protected $itemUrl;
    protected $title;
    private $edition;
    private $publisherPlace;
    private $publisherName;
    private $publicationDate;
    private $provider;
    protected $lang;
    protected $author;
    protected $coAuthors;

    public function __construct($titleUrl, $itemUrl, $title, $edition, $publisherPlace, $publisherName, $publicationDate, $provider, $lang) {
        $this->titleUrl = $titleUrl;
        $this->itemUrl = $itemUrl;
        $this->title = $title;
        $this->edition = $edition;
        $this->publisherPlace = $publisherPlace;
        $this->publisherName = $publisherName;
        $this->publicationDate = $publicationDate;
        $this->provider = $provider;
        $this->lang = $lang;
        $this->coAuthors = array();
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

    public function getProvider() {
        return $this->provider;
    }

    public function getProvListItem() {
        return '<li>Provided by: ' . $this->provider . '</li>';
    }

    public function getAuthorListItem() {
        if($this->author !== null) {
            $list = 'Main: ' . $this->author->getName() . ' - ';
        }
        foreach($this->coAuthors as $author) {
            if($author !== null) {
                $list .= 'Co: ' . $author->getName() . ' - ';
            }
        }
        $list = rtrim($list, ' - ');
        $list = rtrim($list, ',');
        return '<li>By: ' . $list . '</li>';
    }

}