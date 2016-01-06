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

    public function getAPIResults($title, $author, $year, $language, $isGA) {
        if($isGA) {
            $GALang = $this->model->changeLangValue($language);
            $url = $this->model->getUrl($title, $author, $year, $GALang, $isGA);
        } else {
            $url = $this->model->getUrl($title, $author, $year, $language, $isGA);
        }
        $results = $this->model->getAPIResults($url, $isGA);
        if($isGA) {
            $books = $this->model->createGABooks($results);
        } else {
            $books = $this->model->createBHLBooks($results);
        }
        return $books;
    }

    public function start() {
        if($this->view->userPressedSubmit()) {
            //$GAResults = $this->model->getFileResults(__DIR__ . '/../model/results.json');
            //$BHLResults = $this->model->getFileResults( __DIR__ . '/../model/bhlresults.json');
            $title = $this->view->getTitle();
            $author = $this->view->getAuthor();
            $year = $this->view->getYear();
            $language = $this->view->getLanguage();
            if($this->model->inputEmpty($title, $author)) {
                header('Location: index.php');
            } else {
                if($this->model->yearOk($year)) {
                    if(!empty($title)) {
                        $titleId = $this->model->searchTermInDB($title);
                        if($titleId !== null) {
                            //$BHLBooks = $this->model->getDBBooks($titleId, $author, $year, $language, false);
                            $GABooks = $this->model->getDBBooks($titleId, $author, $year, $language, true);
                        } else {
                            $BHLBooks = $this->getAPIResults($title, $author, $year, $language, false);
                            $GABooks = $this->getAPIResults($title, $author, $year, $language, true);
                            if(empty($author) && empty($year) && $language === "NONE") {
                                if((!empty($BHLBooks) && !empty($GABooks)) || !empty($BHLBooks) || !empty($GABooks)) {
                                    $this->model->saveResultsinDB($title, $BHLBooks, $GABooks);
                                }
                            }
                        }
                    } else {
                        $BHLBooks = $this->getAPIResults($title, $author, $year, $language, false);
                        $GABooks = $this->getAPIResults($title, $author, $year, $language, true);
                    }
                    $this->model->saveResultsinSession($BHLBooks, $GABooks);
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