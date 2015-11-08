<?php
require_once("Model.php");
require_once("View.php");

class Controller {
    private $model;
    private $view;

    //Konstruktor
    public function __construct() {
        $this->model = new Model();
        $this->view = new View($this->model);
    }

    public function control(){

        if($this->view->userPressedSubmit()) {
            $url = $this->view->getURL();

            $page = $this->model->getPage($url);

            $menuLinks = $this->model->getMenuLinks($page);

            $friendArray = $this->model->getFriendLinks($menuLinks);

            $friendDates = $this->model->getFriendDates($friendArray);

            $friendMovieDays = $this->model->getFriendMovieDays($friendDates);

            $movies = $this->model->getMovies($friendMovieDays, $menuLinks);

        } else {
            $ret = $this->view->showURLForm();
        }

        return $ret;
    }

}