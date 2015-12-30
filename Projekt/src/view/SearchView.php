<?php

class SearchView
{
    private $model;
    private $button;
    private $titleField;
    private $authorField;
    private $yearField;
    private $langField;

    //Konstruktor
    public function __construct(SearchModel $model) {
        $this->model = $model;
        $this->button = "submitButton";
        $this->titleField = "title";
        $this->authorField = "author";
        $this->yearField = "year";
        $this->langField = "lang";
    }

    public function userPressedSubmit() {
        if(isset($_POST[$this->button])) {
            return true;
        } else {
            return false;
        }
    }

    public function getTitle() {
        if (isset($_POST[$this->titleField])) {
            $title = filter_var(trim($_POST[$this->titleField]), FILTER_SANITIZE_STRING);
            return $title;
        } else {
            exit();
        }
    }

    public function getAuthor() {
        if (isset($_POST[$this->authorField])) {
            $author = filter_var(trim($_POST[$this->authorField]), FILTER_SANITIZE_STRING);
            return $author;
        } else {
            exit();
        }
    }

    public function getYear() {
        if (isset($_POST[$this->yearField])) {
            $year = filter_var(trim($_POST[$this->yearField]), FILTER_SANITIZE_STRING);
            return $year;
        } else {
            exit();
        }
    }

    public function getLanguage() {
        if (isset($_POST[$this->langField])) {
            $language = filter_var(trim($_POST[$this->langField]), FILTER_SANITIZE_STRING);
            return $language;
        } else {
            exit();
        }
    }

    public function showResults($GAResults, $BHLResults) {
        $GAList = '';
        if(empty($GAResults)) {
            $GAList = '<p>No results found</p>';
        } else {
            $gList = '';
            foreach ($GAResults as $result) {
                $gList .= '<li>' . $result->title[0] . ' <a href="' . $result->edmIsShownAt[0] . '" target="_blank">Go to result</a></li>';
            }
            $GAList = '<ul>' . $gList . '</ul>';
        }

        $BHLList = '';
        if(empty($BHLResults)) {
            $BHLList = '<p>No results found</p>';
        } else {
            $bList = '';
            foreach ($BHLResults as $bResult) {
                $bList .= '<li>' . $bResult->FullTitle . ' <a href="' . $bResult->TitleUrl . '" target="_blank">Go to result</a></li>';
            }
            $BHLList = '<ul>' . $bList . '</ul>';
        }
        return '<div class="row"><div class="col-md-12"><h4>Results from Gallica</h4>' . $GAList . '<h4>Results from BHL</h4>' . $BHLList . '</div></div>';
    }

    //Visar formulär
    //http://stackoverflow.com/questions/10794362/trying-to-build-a-toggle-button-that-will-show-and-hide-a-div-box-using-bootstra
    public function showSearchForm() {
        $ret = '            <div class="row">
                                <div class="col-md-12">
                                    <div class="well">
                                        <form class="form-horizontal" action="index.php?results" method="post">
							                <fieldset>
							                    <legend>Search</legend>
                                                <div class="btn-group" data-toggle="buttons-checkbox">
                                                    <a class="btn collapse-data-btn" data-toggle="collapse" href="#advsearch">Show filtering options</a>
                                                </div>
                                                <div class="form-group">
							                        <label class="col-md-2 control-label">Title: </label>
                                                    <div class="col-md-10">
							                            <input class="form-control" type="text" name="' . $this->titleField . '"/>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="col-md-2 control-label">Author: </label>
                                                    <div class="col-md-10">
							                            <input class="form-control" type="text" name="' . $this->authorField . '"/>
                                                    </div>
                                                </div>
                                                <div id="advsearch" class="collapse">
                                                    <div class="form-group">
                                                        <label class="col-md-2 control-label">Year: </label>
                                                        <div class="col-md-10">
							                                <input class="form-control" type="text" name="' . $this->yearField . '"/>
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="col-md-2 control-label">Language: </label>
                                                        <div class="col-md-10">
							                                <select class="form-control" name="' . $this->langField . '">
                                                                <option value="NONE">-- Select a language --</option>
                                                                <option value="DUT">Dutch</option>
                                                                <option value="ENG">English</option>
                                                                <option value="FRE">French</option>
                                                                <option value="GER">German</option>
                                                                <option value="ITA">Italian</option>
                                                                <option value="RUS">Russian</option>
                                                                <option value="SPA">Spanish</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
							                </fieldset>
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="pull-right">
                                                        <input type="submit" class="btn btn-primary" name="' . $this->button . '" value="Submit"/>
                                                    </div>
                                                </div>
                                            </div>
							            </form>
                                    </div>
                                </div>
                            </div>';
        return $ret;
    }

}