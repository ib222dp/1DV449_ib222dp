<?php

class TableView extends View
{
    private $model;
    private $backLink;

    //Konstruktor
    public function __construct(Model $model) {
        $this->model = $model;
        $this->backLink = "<a href='index.php?movies'>Tillbaka</a>";
    }

    //Hämtar vald dag från url:en
    public function getMovieDay() {
        if(isset($_GET[self::$dayParam])) {
            $day = $_GET[self::$dayParam];
            return $day;
        } else {
            exit;
        }
    }

    //Hämtar vald tid från url:en
    public function getMovieTime() {
        if(isset($_GET[self::$timeParam])) {
            $time = $_GET[self::$timeParam];
            return $time;
        } else {
            exit;
        }
    }

    //Hämtar vald tid för ett bord från url:en
    public function getBookTime() {
        if(isset($_GET[self::$bookTimeParam])) {
            $time = $_GET[self::$bookTimeParam];
            return $time;
        } else {
            exit;
        }
    }

    //Visar lediga bord för den valda dagen och tiden
    public function showTables($tables) {
        if(empty($tables)) {
            $ret = $this->backLink . "<p>Inga lediga bord finns</p>";
        }else {
            $header = "<h1>Följande tider hittades</h1><ul>";
            $list = '';
            foreach ($tables as $table) {
                $list .= "<li>Det finns ett ledigt bord <b>kl " . $table->getTime() . "</b>
                    <a href='?" . self::$bookTimeParam . "=" . $table->getValue() .
                    "'>Boka detta bord</a></li>";
            }
            $ul = "</ul>";
            $ret = $this->backLink . $header . $list . $ul;
        }
        return $ret;
    }

    //Visas när ett bord har bokats
    public function showResponse($data) {
        return $this->backLink . "<p>" . $data . "</p>";
    }

}