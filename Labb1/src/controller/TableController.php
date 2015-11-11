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

    public function start(){
        //Hämtar vald dag och tid från url:en
        $day = $this->view->getMovieDay();
        $time = $this->view->getMovieTime();

        $menuLinks = $this->model->getMenuLinks($_SESSION["givenURL"]);

        //Hämtar och visar lediga bord för den valda dagen och tiden
        $tableTimes = $this->model->getTableTimes($day, $time, $menuLinks);
        $ret = $this->view->showTableTimes($tableTimes);

        return $ret;
    }

}