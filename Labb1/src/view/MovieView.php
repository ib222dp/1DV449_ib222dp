<?php

class MovieView extends View
{
    private $model;

    //Konstruktor
    public function __construct(Model $model) {
        $this->model = $model;
    }

    //Kontrollerar om användaren har valt en film (om query-parametern "day" finns i url:en)
    public function movieChosen() {
        if(array_key_exists(self::$dayParam, $_GET)) {
            return true;
        }else {
            return false;
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

    //Kontrollerar om query-parametern "movies" finns i url:en
    public function moviesListed() {
        if(array_key_exists(self::$movieParam, $_GET)) {
            return true;
        }else {
            return false;
        }
    }

    //Hämtar url
    public function getURL()
    {
        if (isset($_POST["url"])) {
            $url = filter_var(trim($_POST["url"]), FILTER_SANITIZE_STRING);
            return $url;
        } else {
            exit();
        }
    }

    public function showValPage(){
        return  "<a href='index.php'>Tillbaka</a>
                <p>URL saknas</p>";
    }

    //Visar de filmer som går när alla tre är lediga
    public function showMovies($movies) {
        $link = "<a href='index.php'>Tillbaka</a>";
        $header = "<h1>Följande filmer hittades</h1><ul>";
        $list = '';
        foreach ($movies as $movie) {
            $list .= "<li>Filmen <b>" . $movie->movie . "</b> klockan " . $movie->time . " på "
                . $movie->day . " <a href='?day=" . $movie->day . "&time=" . $movie->time
                . "'>Välj denna film och boka bord</a></li>";
        }
        $ul = "</ul>";
        $ret = $link . $header . $list . $ul;
        return $ret;
    }

    //Visar formulär för att ange URL
    public function showURLForm() {
        $ret = "
                            <form action='index.php?movies' method='post'>
							<fieldset>
							<legend>Labb1</legend>
							<label>Ange URL: </label>
							<input type='text' name='url'/>
							<input type='submit' name='submitButton' value='Ange url'/>
							</fieldset>
							</form>";
        return $ret;
    }

}