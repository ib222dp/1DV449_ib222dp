<?php
require_once("src/model/SearchModel.php");
require_once("src/view/SearchView.php");

class SearchController {
    private $model;
    private $view;

    //Konstruktor
    public function __construct() {
        $this->model = new SearchModel();
        $this->view = new SearchView($this->model);
    }

    public function start() {
        if($this->view->userPressedSubmit()) {
            $param = $this->view->getParam();
            $gallicaResults = $this->model->getGallicaResults($param);
            $BHLResults = $this->model->getBHLResults($param);
            if(empty($gallicaResults) && empty($BHLResults)) {
                $ret = $this->view->showNoResults();
            } else {
                $ret = $this->view->showResults($gallicaResults, $BHLResults);
            }
        } else {
            $this->model->destroySession();
            $ret = $this->view->showSearchForm();
        }
        return $ret;
    }
}