<?php

require_once("BHLKey.php");

class BHLUrl {

    private $start;
    private $end;
    private $BHLKey;
    private $title;
    private $auth;
    private $year;
    private $lang;

    //Konstruktor
    public function __construct() {
        $this->BHLKey = new BHLKey();
        $this->start = 'http://www.biodiversitylibrary.org/api2/httpquery.ashx?op=BookSearch';
        $this->end = '&apikey=' . $this->BHLKey->getValue() . '&format=json';
        $this->title = '&title=';
        $this->auth = '&lname=';
        $this->year = '&year=';
        $this->lang = '&language=';
    }

    public function getStart() {
        return $this->start;
    }

    public function getEnd() {
        return $this->end;
    }

    public function getTitle() {
        return $this->title;
    }

    public function getAuth() {
        return $this->auth;
    }

    public function getYear() {
        return $this->year;
    }

    public function getLang() {
        return $this->lang;
    }

}