<?php

abstract class View
{
    protected static $dayParam = "day";
    protected static $timeParam = "time";
    protected static $movieParam = "movies";
    protected static $bookDayParam = "bookday";
    protected static $bookTimeParam = "booktime";

    //Konstruktor
    public function __construct() {

    }

    //Kontrollerar om användaren har valt en film (om query-parametrarna "day" och "time" finns i url:en)
    public function dayAndTimeChosen() {
        if(array_key_exists(self::$dayParam, $_GET) && array_key_exists(self::$timeParam, $_GET)) {
            return true;
        }else {
            return false;
        }
    }

    public function bookTimeChosen() {
        if(array_key_exists(self::$bookTimeParam, $_GET) && array_key_exists(self::$bookDayParam, $_GET) ) {
            return true;
        }else {
            return false;
        }
    }

}