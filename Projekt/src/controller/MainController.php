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

    //Hämtar böcker från API:erna
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
                        //Kontrollerar om titeln redan finns i databasen
                        $DBTitle = $this->DBController->getSearchTerm($title);
                        if(is_array($DBTitle)) {
                            //Hämtar resultat från cachen om de inte är för gamla
                            if($DBTitle[1] === true){
                                $books = $this->DBController->getBooks($DBTitle[0]->Id, $author, $year, $language);
                                $BHLBooks = $books[0];
                                $GABooks = $books[1];
                                //Om resultaten i databasen är för gamla hämtas nya resultat från API:erna och sparas i databasen
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
                            //Om titeln inte fanns i databasen hämtas resultat från API:erna och sparas i databasen
                            $BHLBooks = $this->getAPIResults($title, $author, $year, $language, false);
                            $GABooks = $this->getAPIResults($title, $author, $year, $language, true);
                            if(empty($author) && empty($year) && $language === "NONE") {
                                if(!empty($BHLBooks) || !empty($GABooks)) {
                                    $this->DBController->saveResults($title, $BHLBooks, $GABooks, true);
                                }
                            }
                        }
                    } else {
                        //Om användaren inte har angett någon titel hämtas resultaten från API:erna och sparas ej i databasen
                        $BHLBooks = $this->getAPIResults($title, $author, $year, $language, false);
                        $GABooks = $this->getAPIResults($title, $author, $year, $language, true);
                    }
                    $this->model->saveResultsinSession($BHLBooks, $GABooks);
                }
                //PRG
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