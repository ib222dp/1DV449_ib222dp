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

        if($this->view->userPressedSubmit() || $this->view->moviesListed()){

            if($this->view->userPressedSubmit()) {
                $url = $this->view->getURL();
                if($this->model->inputOK($url)) {
                    $this->model->setURL($url);
                }else {
                    $ret = $this->view->showValPage();
                }

            }else {
                $url = $_SESSION["givenURL"];
            }

            $menuLinks = $this->model->getMenuLinks($url);

            $friendArray = $this->model->getFriendLinks($menuLinks);

            $dayLists = $this->model->getFriendDays($friendArray);

            $movieDays = $this->model->calculateMovieDays($dayLists);

            $movies = $this->model->getMovies($movieDays, $menuLinks);

            $ret = $this->view->showMovies($movies);

        } else {
            $this->model->destroySession();
            $ret = $this->view->showURLForm();
        }

        return $ret;
    }

}