<?php

class MovieView extends View
{
    private $model;
    private $urlForm;

    //Konstruktor
    public function __construct(Model $model) {
        $this->model = $model;

        $this->urlForm .= "
                            <form action='index.php?movies' method='post'>
							<fieldset>
							<legend>Labb1</legend>
							<label>Ange URL: </label>
							<input type='text' name='url'/>
							<input type='submit' name='submitButton' value='Ange url'/>
							</fieldset>
							</form>";
    }

    //Visar formulär för att ange URL
    public function showURLForm() {
        $ret = $this->urlForm;
        return $ret;
    }

    public function moviesListed() {
        if(array_key_exists(self::$movieParam, $_GET)) {
            return true;
        }else {
            return false;
        }
    }

    public function movieChosen() {
        if(array_key_exists(self::$dayParam, $_GET)) {
            return true;
        }else {
            return false;
        }
    }

    public function showMovies($movies) {

        $link = "<a href='index.php'>Tillbaka</a>";

        $header = "<h1>Följande filmer hittades</h1><ul>";

        $list = null;

        foreach ($movies as $movie) {
            $list .= "<li>Filmen <b>" . $movie->movie . "</b> klockan " . $movie->time . " på "
                . $movie->day . " <a href='?day=" . $movie->day . "&time=" . $movie->time . "'>Välj denna film och boka bord</a></li>";
        }

        $ul = "</ul>";

        $ret = $link . $header . $list . $ul;

        return $ret;

    }

    //Hämtar url
    public function getURL()
    {
        if (isset($_POST["url"])) {
            $url = filter_var(trim($_POST["url"]), FILTER_SANITIZE_STRING);
            $_SESSION["givenURL"] = $url;
            return $url;
        } else {
            exit();
        }
    }

    //Kontrollerar om användaren klickat på "Ange URL"
    public function userPressedSubmit() {
        if(isset($_POST["submitButton"])){
            return true;
        }else{
            return false;
        }
    }

}