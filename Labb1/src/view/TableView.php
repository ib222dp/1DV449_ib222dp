<?php

class TableView extends View
{
    private $model;

    //Konstruktor
    public function __construct(Model $model) {
        $this->model = $model;
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

    //Visar lediga bord för den valda dagen och tiden
    public function showTableTimes($tableTimes) {

        $link = "<a href='index.php?movies'>Tillbaka</a>";

        if(empty($tableTimes)) {
            $ret = $link . "<p>Inga lediga bord finns</p>";
        }else {
            $header = "<h1>Följande tider hittades</h1><ul>";

            $list = '';

            foreach ($tableTimes as $time) {

                $list .= "<li>Det finns ett ledigt bord <b>kl " . $time . "</b></li>";
            }

            $ul = "</ul>";

            $ret = $link . $header . $list . $ul;
        }

        return $ret;
    }

}