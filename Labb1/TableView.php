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

}