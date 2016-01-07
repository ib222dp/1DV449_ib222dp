<?php
require_once("src/model/BBOModel.php");
require_once("src/model/DBModel.php");
require_once("src/view/DBView.php");

class DBController {
    private $model;
    private $view;

    public function __construct() {
        $this->model = new DBModel();
        $this->view = new DBView($this->model);
    }

    public function getSearchTerm($title) {
        $titleId = $this->model->getSearchTerm($title);
        return $titleId;
    }

    public function getBooks($titleId, $author, $year, $language) {
        $BHLBooks = $this->model->getBooks($titleId, $author, $year, $language, false);
        $GABooks = $this->model->getBooks($titleId, $author, $year, $language, true);
        return array($BHLBooks, $GABooks);
    }

    public function saveResults($title, $BHLBooks, $GABooks) {
        $this->model->saveResults($title, $BHLBooks, $GABooks);
    }

}