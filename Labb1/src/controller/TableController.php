<?php
require_once("src/model/model.php");
require_once("src/model/TableModel.php");
require_once("src/view/AbView.php");
require_once("src/view/TableView.php");

class TableController {
    private $model;
    private $view;

    //Konstruktor
    public function __construct() {
        $this->model = new TableModel();
        $this->view = new TableView($this->model);
    }

    public function start() {
        if($this->model->URLIsSet() && $this->model->moviesAreSet()) {
            if($this->view->dayAndTimeChosen()) {
                //Hämtar vald dag och tid från url:en
                $day = $this->view->getMovieDay();
                $time = $this->view->getMovieTime();
                //Hämtar och visar lediga bord för den valda dagen och tiden
                $tableTimes = $this->model->getTableTimes($day, $time);
                $ret = $this->view->showTableTimes($tableTimes, $day);
            } else {
                $url = $this->model->getSavedURL() . "/dinner/login";
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_USERAGENT, "ib222dp");
                curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_POST, true);

                $postFields = array("group1" => "lor1820", "username" => "zeke", "password" => "coys",
                "submit" => "login");
                curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);

                $data = curl_exec($ch);
                curl_close($ch);
                $ret = $data;
            }
            return $ret;
        } else {
            $this->model->destroySession();
            header('location: ' . $_SERVER['PHP_SELF']);
            die;
        }
    }

}