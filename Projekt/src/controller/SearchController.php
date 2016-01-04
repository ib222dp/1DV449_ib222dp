<?php
require_once("src/model/SearchModel.php");
require_once("src/view/SearchView.php");

class SearchController {
    private $model;
    private $view;

    public function __construct() {
        $this->model = new SearchModel();
        $this->view = new SearchView($this->model);
    }

    public function start() {
        if($this->view->userPressedSubmit()) {
            //$GAResults = $this->model->getFileResults(__DIR__ . '/../model/results.json');
            //$BHLResults = $this->model->getFileResults( __DIR__ . '/../model/bhlresults.json');
            $title = $this->view->getTitle();
            $author = $this->view->getAuthor();
            $year = $this->view->getYear();
            $language = $this->view->getLanguage();
            if($this->model->inputEmpty($title, $author)){
                $ret = array($this->view->showEmptyValPage(), true);
            } else {
                $BHLUrl = $this->model->getUrl($title, $author, $year, $language, false);
                $newLang = $this->model->changeLangValue($language);
                $GAUrl = $this->model->getUrl($title, $author, $year, $newLang, true);
                $GAResults = $this->model->getAPIResults($GAUrl, true);
                $BHLResults = $this->model->getAPIResults($BHLUrl, false);
                $BHLBooks = $this->model->createBHLBooks($BHLResults);
                $GABooks = $this->model->createGABooks($GAResults);
                $ret = array($this->view->showResults($GABooks, $BHLBooks), false);
            }
        } else {
            $this->model->destroySession();
            $ret = array($this->view->showSearchForm(), true);
        }
        return $ret;
    }
}