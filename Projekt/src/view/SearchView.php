<?php

class SearchView
{
    private $model;
    private $button;
    private $searchField;

    //Konstruktor
    public function __construct(SearchModel $model) {
        $this->model = $model;
        $this->button = "submitButton";
        $this->searchField = "search";
    }

    public function userPressedSubmit() {
        if(isset($_POST[$this->button])) {
            return true;
        } else {
            return false;
        }
    }

    public function getParam() {
        if (isset($_POST[$this->searchField])) {
            $param = filter_var(trim($_POST[$this->searchField]), FILTER_SANITIZE_STRING);
            return $param;
        } else {
            exit();
        }
    }

    public function showNoResults() {
        return '<p>No results found</p>';
    }

    public function showResults($gallicaResults, $BHLResults) {
        $gList = '';
        foreach ($gallicaResults as $result) {
            $gList .= '<li>' . $result->title[0] . '</li><ul><li><a href="' . $result->edmIsShownAt[0] .
                '" target="_blank">Go to result</a></li></ul>';
        }
        $gallicaList = '<ul class="list-unstyled">' . $gList . '</ul>';

        $bList = '';
        foreach ($BHLResults as $bResult) {
            $bList .= '<li>' . $bResult->FullTitle . '</li><ul><li><a href="' . $bResult->TitleUrl .
                '" target="_blank">Go to result</a></li></ul>';
        }
        $BHLList = '<ul class="list-unstyled">' . $bList . '</ul>';

        return '<h4>Results from Gallica</h4>' . $gallicaList . '<h4>Results from BHL</h4>' . $BHLList;
    }

    //Visar formulär för att ange URL
    public function showSearchForm() {
        $ret = '
                            <form action="index.php?results" method="post">
							<fieldset>
							<legend>Search</legend>
							<label>Title: </label>
							<input type="text" name="' . $this->searchField . '"/>
							<input type="submit" name="' . $this->button . '" value="Submit"/>
							</fieldset>
							</form>';
        return $ret;
    }

}