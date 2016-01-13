<?php
require_once("src/model/MainModel.php");
require_once("src/model/DBModel.php");

class DBController {
    private $model;

    public function __construct() {
        $this->model = new DBModel();
    }

    public function getSearchTerm($title) {
        $DBTitle = $this->model->getSearchTerm($title);
        return $DBTitle;
    }

    public function getBooks($titleId, $author, $year, $language) {
        $BHLBooks = $this->model->getBooks($titleId, $author, $year, $language, false);
        $GABooks = $this->model->getBooks($titleId, $author, $year, $language, true);
        return array($BHLBooks, $GABooks);
    }

    public function saveResults($title, $BHLBooks, $GABooks, $isNewTitle) {
        $this->model->saveResults($title, $BHLBooks, $GABooks, $isNewTitle);
    }

    public function deleteSearchTerm($titleId) {
        $this->model->deleteSearchTerm($titleId);
    }

}