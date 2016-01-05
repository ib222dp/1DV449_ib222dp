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
                header('Location: index.php');
            } else {
                if($this->model->yearOk($year)) {
                    if($this->model->searchTermInDB) {
                        $BHLBooks = $this->model->getDBBHLResults($title, $author, $year, $language);
                        $GABooks = $this->model->getDBGAResults($title, $author, $year, $language);
                    } else {
                        $BHLUrl = $this->model->getUrl($title, $author, $year, $language, false);
                        $GALang = $this->model->changeLangValue($language);
                        $GAUrl = $this->model->getUrl($title, $author, $year, $GALang, true);
                        $BHLResults = $this->model->getAPIResults($BHLUrl, false);
                        $GAResults = $this->model->getAPIResults($GAUrl, true);
                        $BHLBooks = $this->model->createBHLBooks($BHLResults);
                        $GABooks = $this->model->createGABooks($GAResults);
                        //save to DB, connect to saved searchterm
                    }
                    $this->model->saveResults($BHLBooks, $GABooks);
                }
                header('Location: index.php');
            }
        } else {
            $errormsg = $this->model->getErrorMessage();
            $BHLBooks = $this->model->getSavedBHLBooks();
            $GABooks = $this->model->getSavedGABooks();
            if(isset($errormsg) && $errormsg !== 0) {
                $this->model->destroySession();
                $ret = array($this->view->showEmptyValPage($errormsg), true);
            } elseif(isset($BHLBooks) && isset($GABooks)) {
                $this->model->destroySession();
                $ret = array($this->view->showResults($GABooks, $BHLBooks), false);
            }else {
                $this->model->destroySession();
                $ret = array($this->view->showSearchForm(), true);
            }
        }
        return $ret;
    }
}