<?php
require_once("src/model/MainModel.php");
require_once("src/model/DBModel.php");

class DBController {
    private $model;

    public function __construct() {
        $this->model = new DBModel();
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

    public function saveNewResults($titleId, $BHLBooks, $GABooks) {
        $this->model->saveNewResults($titleId, $BHLBooks, $GABooks);
    }

    public function deleteSearchTerm($titleId) {
        $this->model->deleteSearchTerm($titleId);
    }

}