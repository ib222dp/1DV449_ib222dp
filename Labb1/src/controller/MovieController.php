<?php
require_once("src/model/model.php");
require_once("src/model/MovieModel.php");
require_once("src/view/AbView.php");
require_once("src/view/MovieView.php");

class MovieController {
    private $model;
    private $view;

    //Konstruktor
    public function __construct() {
        $this->model = new MovieModel();
        $this->view = new MovieView($this->model);
    }

    //Kontrollerar om användaren har valt en film
    public function movieChosen() {
        if($this->view->movieChosen()){
            return true;
        } else {
            return false;
        }
    }

    public function start(){
        if($this->view->userPressedSubmit()){
            $startURL = $this->view->getURL();
            if($this->model->inputOK($startURL)) {
                $this->model->setURL($startURL);
                $ret = $this->showMovieList();
            }else {
                $ret = $this->view->showValPage();
            }
        } elseif($this->view->moviesListed()) {
            $ret = $this->showMovieList();
        } else {
            $this->model->destroySession();
            $ret = $this->view->showURLForm();
        }
        return $ret;
    }

    public function showMovieList(){
        $friends = $this->model->getFriends();
        $dayLists = $this->model->getDayLists($friends);
        $movieDays = $this->model->calculateMovieDays($dayLists);
        $movies = $this->model->getMovies($movieDays);
        $ret = $this->view->showMovies($movies);
        return $ret;
    }

}