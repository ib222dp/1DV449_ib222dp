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
    public function dayAndTimeChosen() {
        if($this->view->dayAndTimeChosen()){
            return true;
        } else {
            return false;
        }
    }

    //Kontrollerar om query-parametern "movies" finns i url:en
    public function moviesListed() {
        if($this->view->moviesListed()){
            return true;
        } else {
            return false;
        }
    }

    public function bookTimeChosen() {
        if($this->view->bookTimeChosen()){
            return true;
        } else {
            return false;
        }
    }

    public function start() {
        if($this->view->userPressedSubmit()) {
            $startURL = $this->view->getURL();
            if($this->model->inputOK($startURL)) {
                $this->model->setURL($startURL);
                $ret = $this->showMovieList();
            }else {
                $ret = $this->view->showValPage();
            }
        } elseif($this->view->moviesListed()) {
            if($this->model->URLIsSet() && $this->model->moviesAreSet()) {
                $movies = $this->model->getSavedMovies();
                $ret = $this->view->showMovies($movies);
            }else {
                $this->model->destroySession();
                header('location: ' . $_SERVER['PHP_SELF']);
                die;
            }
        } else {
            $this->model->destroySession();
            $ret = $this->view->showURLForm();
        }
        return $ret;
    }

    public function showMovieList() {
        $friends = $this->model->getFriends();
        $dayLists = $this->model->getDayLists($friends);
        $movieDays = $this->model->calculateMovieDays($dayLists);
        if(empty($movieDays)) {
            $ret = $this->view->showNoFreeDays();
        } else {
            $movies = $this->model->getMovies($movieDays);
            if(empty($movies)) {
                $ret = $this->view->showNoMovies();
            } else {
                $this->model->setMovies($movies);
                $ret = $this->view->showMovies($movies);
            }
        }
        return $ret;
    }

}