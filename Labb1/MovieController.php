<?php
require_once("model.php");
require_once("AbView.php");
require_once("MovieView.php");

class MovieController {
    private $model;
    private $view;

    //Konstruktor
    public function __construct() {
        $this->model = new Model();
        $this->view = new MovieView($this->model);
    }

    public function moviesChosen() {
        if($this->view->moviesChosen()){
            return true;
        } else {
            return false;
        }
    }

    public function start(){

        if($this->view->userPressedSubmit()) {
            $url = $this->view->getURL();

            $page = $this->model->getPage($url);

            $menuLinks = $this->model->getMenuLinks($page);

            $friendArray = $this->model->getFriendLinks($menuLinks);

            $friendDates = $this->model->getFriendDates($friendArray);

            $friendMovieDays = $this->model->getFriendMovieDays($friendDates);

            $movies = $this->model->getMovies($friendMovieDays, $menuLinks);

            $ret = $this->view->showMovies($movies);

        } else {
            $ret = $this->view->showURLForm();
        }

        return $ret;
    }

}