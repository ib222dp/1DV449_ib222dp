<?php

class TableView extends View
{
    private $model;

    //Konstruktor
    public function __construct(Model $model) {
        $this->model = $model;
    }

    public function getMovieDay() {
        if(isset($_GET[self::$dayParam])) {
            $day = $_GET[self::$dayParam];
            return $day;
        } else {
            exit;
        }
    }

    public function getMovieTime() {
        if(isset($_GET[self::$timeParam])) {
            $time = $_GET[self::$timeParam];
            return $time;
        } else {
            exit;
        }
    }

    public function showTableTimes($tableTimes) {

        $link = "<a href='index.php?movies'>Tillbaka</a>";

        $header = "<h1>Följande tider hittades</h1><ul>";

        if(empty($tableTimes)) {
            $ret = $link . "<p>Inga lediga bord finns</p>";
        }else {
            $list = null;

            foreach ($tableTimes as $time) {

                $list .= "<li>Det finns ett ledigt bord <b>kl " . $time . "</b></li>";
            }

            $ul = "</ul>";

            $ret = $link . $header . $list . $ul;
        }

        return $ret;
    }

}