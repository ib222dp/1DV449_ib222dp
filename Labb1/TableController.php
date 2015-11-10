<?php
require_once("model.php");
require_once("AbView.php");
require_once("TableView.php");

class TableController {
    private $model;
    private $view;

    //Konstruktor
    public function __construct() {
        $this->model = new Model();
        $this->view = new TableView($this->model);
    }

    public function start(){

        $day = $this->view->getMovieDay();
        $time = $this->view->getMovieTime();

        $tables = $this->model->getTable($day, $time);

        return $ret;
    }

}