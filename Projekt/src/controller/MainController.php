<?php
require_once("DBController.php");
require_once("src/model/MainModel.php");
require_once("src/model/APIModel.php");
require_once("src/view/MainView.php");

class MainController {
    private $DBController;
    private $model;
    private $view;


    public function __construct() {
        $this->DBController = new DBController();
        $this->model = new APIModel();
        $this->view = new MainView($this->model);
    }

    public function getAPIResults($title, $author, $year, $language, $isGA) {
        if($isGA) {
            $GALang = $this->model->changeLangValue($language);
            $url = $this->model->getUrl($title, $author, $year, $GALang, $isGA);
        } else {
            $url = $this->model->getUrl($title, $author, $year, $language, $isGA);
        }
        $items = $this->model->getAPIResults($url, $isGA);
        if($items === null) {
            header('Location: index.php');
        } else {
            $books = $this->model->createBooks($items, $isGA);
            return $books;
        }
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
                        $DBTitle = $this->DBController->getSearchTerm($title);
                        if(is_array($DBTitle)) {
                            if($DBTitle[1] === true){
                                $books = $this->DBController->getBooks($DBTitle[0]->Id, $author, $year, $language);
                                $BHLBooks = $books[0];
                                $GABooks = $books[1];
                            } else {
                                $BHLBooks = $this->getAPIResults($title, $author, $year, $language, false);
                                $GABooks = $this->getAPIResults($title, $author, $year, $language, true);
                                if(empty($author) && empty($year) && $language === "NONE" && !empty($BHLBooks) ||
                                    empty($author) && empty($year) && $language === "NONE" && !empty($GABooks)) {
                                    $this->DBController->saveResults($DBTitle[0]->title, $BHLBooks, $GABooks, false);
                                } else {
                                    $this->DBController->deleteSearchTerm($DBTitle[0]->Id);
                                }
                            }
                        } else {
                            $BHLBooks = $this->getAPIResults($title, $author, $year, $language, false);
                            $GABooks = $this->getAPIResults($title, $author, $year, $language, true);
                            if(empty($author) && empty($year) && $language === "NONE") {
                                if(!empty($BHLBooks) || !empty($GABooks)) {
                                    $this->DBController->saveResults($title, $BHLBooks, $GABooks, true);
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