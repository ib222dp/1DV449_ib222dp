<?php

class APIView
{
    private $model;
    private $button;
    private $titleField;
    private $authorField;
    private $yearField;
    private $langField;
    private $searchForm;

    public function __construct(APIModel $model) {
        $this->model = $model;
        $this->button = "submitButton";
        $this->titleField = "title";
        $this->authorField = "author";
        $this->yearField = "year";
        $this->langField = "lang";
        //http://stackoverflow.com/questions/10794362/trying-to-build-a-toggle-button-that-will-show-and-hide-a-div-box-using-bootstra
        $this->searchForm = '<div class="row">
                                <div class="col-md-12">
                                    <div class="well">
                                        <form class="form-horizontal" action="index.php" method="post">
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
                                                    <label class="col-md-2 control-label">Author Last Name: </label>
                                                    <div class="col-md-10">
							                            <input class="form-control" type="text" name="' . $this->authorField . '"/>
                                                    </div>
                                                </div>
                                                <div id="advsearch" class="collapse">
                                                    <div class="form-group">
                                                        <label class="col-md-2 control-label">Year (YYYY): </label>
                                                        <div class="col-md-10">
							                                <input class="form-control" type="text" name="' . $this->yearField . '"/>
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="col-md-2 control-label">Language: </label>
                                                        <div class="col-md-10">
							                                <select class="form-control" name="' . $this->langField . '">
                                                                <option value="NONE">-- Select a language --</option>
                                                                <option value="CHI">Chinese</option>
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

    public function showEmptyValPage($message) {
        return '<ul class="list-group"><li class="list-group-item list-group-item-danger">' . $message . '</li></ul>' . $this->searchForm;
    }

    public function showResults($GABooks, $BHLBooks) {
        $gList = '';
        if(empty($GABooks)) {
            $gList = '<p>No results found</p>';
        } else {
            foreach ($GABooks as $GABook) {
                $title = '<ul class="list-group list-unstyled">' . $GABook->getTitleListItem();
                $author = $GABook->getAuthorListItem();
                $year = $GABook->getYearListItem();
                $url = $GABook->getUrlListItem();
                $ul = '</ul>';
                if($GABook->getAuthor()->getName() !== 'No author') {
                    if(empty($GABook->getYear()) && empty($GABook->getItemUrl())) {
                        $gList .= $title . $author . $ul;
                    } elseif(!empty($GABook->getYear()) && !empty($GABook->getItemUrl())) {
                        $gList .= $title . $author . $year . $url . $ul;
                    } elseif(!empty($GABook->getYear()) && empty($GABook->getItemUrl())) {
                        $gList .= $title . $author . $year . $ul;
                    } elseif(empty($GABook->getYear()) && !empty($GABook->getItemUrl())) {
                        $gList .= $title . $author . $url . $ul;
                    }
                } else {
                    if(empty($GABook->getYear()) && empty($GABook->getItemUrl())) {
                        $gList .= $title . $ul;
                    } elseif(!empty($GABook->getYear()) && !empty($GABook->getItemUrl())) {
                        $gList .= $title . $year . $url . $ul;
                    } elseif(!empty($GABook->getYear()) && empty($GABook->getItemUrl())) {
                        $gList .= $title . $year . $ul;
                    } elseif(empty($GABook->getYear()) && !empty($GABook->getItemUrl())) {
                        $gList .= $title . $url . $ul;
                    }
                }
            }
        }

        $bList = '';
        if(empty($BHLBooks)) {
            $bList = '<p>No results found</p>';
        } else {
            foreach ($BHLBooks as $BHLBook) {
                $bList1 = '<ul class="list-group list-unstyled">' . $BHLBook->getTitleListItem() . $BHLBook->getAuthorListItem();
                $bList2 = $BHLBook->getPubListItem() . $BHLBook->getProvListItem() . $BHLBook->getUrlListItem() . '</ul>';
                if(!empty($BHLBook->getEdition())) {
                    $bList .= $bList1 . $BHLBook->getEditionListItem() . $bList2;
                } else {
                    $bList .= $bList1 . $bList2;
                }
            }

        }
        return '<div class="row"><div class="col-md-6"><h4>Results from Gallica</h4>' . $gList . '</div><div class="col-md-6"><h4>Results from BHL</h4>' . $bList . '</div></div></div>';
    }

    public function showSearchForm() {
        return $this->searchForm;
    }

}