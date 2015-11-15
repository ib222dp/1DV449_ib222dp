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
                $day = $this->view->getMovieDay();
                $time = $this->view->getMovieTime();
                $tables = $this->model->getTables($day, $time);
                $ret = $this->view->showTables($tables);
            } else {
                $bookTime = $this->view->getBookTime();
                $formURL = $this->model->getFormURL();
                $postFields = $this->model->getPostFields($bookTime);
                $response = $this->model->postData($formURL, $postFields);
                $ret = $this->view->showResponse($response);
            }
            return $ret;
        } else {
            $this->model->destroySession();
            header('location: ' . $_SERVER['PHP_SELF']);
            die;
        }
    }

}