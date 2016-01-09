<?php
require_once("src/model/MainModel.php");
require_once("src/model/APIModel.php");
require_once("src/view/APIView.php");
require_once("DBController.php");

class SearchController {
    private $model;
    private $view;
    private $DBController;

    public function __construct() {
        $this->model = new APIModel();
        $this->view = new APIView($this->model);
        $this->DBController = new DBController();
    }

    public function getAPIResults($title, $author, $year, $language, $isGA) {
        if($isGA) {
            $items = $this->model->getFileResults(__DIR__ . '/../model/results.json');
            //$GALang = $this->model->changeLangValue($language);
            //$url = $this->model->getUrl($title, $author, $year, $GALang, $isGA);
        } else {
            $items = $this->model->getFileResults( __DIR__ . '/../model/bhlresults.json');
            //$url = $this->model->getUrl($title, $author, $year, $language, $isGA);
        }
        //$items = $this->model->getAPIResults($url, $isGA);
        $books = $this->model->createBooks($items, $isGA);
        return $books;
    }

    public function start() {
        if($this->view->userPressedSubmit()) {
            $title = $this->view->getTitle();
            $author = $this->view->getAuthor();
            $year = $this->view->getYear();
            $language = $this->view->getLanguage();
            if($this->model->inputEmpty($title, $author)) {
                header('Location: index.php');
            } else {
                if($this->model->yearOk($year)) {
                    if(!empty($title)) {
                        $titleId = $this->DBController->getSearchTerm($title);
                        if(is_array($titleId)) {
                            if($titleId[1] == true){
                                $books = $this->DBController->getBooks($titleId[0], $author, $year, $language);
                                $BHLBooks = $books[0];
                                $GABooks = $books[1];
                            } else {
                                $BHLBooks = $this->getAPIResults($title, $author, $year, $language, false);
                                $GABooks = $this->getAPIResults($title, $author, $year, $language, true);
                                /*if(empty($author) && empty($year) && $language === "NONE" && !empty($BHLBooks) ||
                                   empty($author) && empty($year) && $language === "NONE" && !empty($GABooks)) {
                                    $this->DBController->saveNewResults($titleId[0], $BHLBooks, $GABooks);
                                } else {
                                    $this->DBController->deleteSearchTerm($titleId[0]);
                                }*/
                            }
                        } else {
                            $BHLBooks = $this->getAPIResults($title, $author, $year, $language, false);
                            $GABooks = $this->getAPIResults($title, $author, $year, $language, true);
                            /*if(empty($author) && empty($year) && $language === "NONE") {
                                if(!empty($BHLBooks) || !empty($GABooks)) {
                                    $this->DBController->saveResults($title, $BHLBooks, $GABooks);
                                }
                            }*/
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