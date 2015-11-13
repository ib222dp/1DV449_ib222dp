<?php

class MovieView extends View
{
    private $model;
    private $button;
    private $urlField;

    //Konstruktor
    public function __construct(Model $model) {
        $this->model = $model;
        $this->button = "submitButton";
        $this->urlField = "url";
    }

    //Kontrollerar om query-parametern "movies" finns i url:en
    public function moviesListed() {
        if(array_key_exists(self::$movieParam, $_GET)) {
            return true;
        }else {
            return false;
        }
    }

    //Kontrollerar om användaren klickat på "Ange URL"
    public function userPressedSubmit() {
        if(isset($_POST[$this->button])){
            return true;
        }else{
            return false;
        }
    }

    //Hämtar url
    public function getURL()
    {
        if (isset($_POST[$this->urlField])) {
            $url = filter_var(trim($_POST[$this->urlField]), FILTER_SANITIZE_STRING);
            return $url;
        } else {
            exit();
        }
    }

    //Visas om url ej har angetts
    public function showValPage(){
        return  "<a href='index.php'>Tillbaka</a>
                <p>URL saknas</p>";
    }

    //Visar formulär för att ange URL
    public function showURLForm() {
        $ret = "
                            <form action='index.php?movies' method='post'>
							<fieldset>
							<legend>Labb1</legend>
							<label>Ange URL: </label>
							<input type='text' name='" . $this->urlField . "'/>
							<input type='submit' name='" . $this->button . "' value='Ange url'/>
							</fieldset>
							</form>";
        return $ret;
    }

    //Visas om det inte finns någon dag när alla tre är lediga
    public function showNoFreeDays() {
        return "<a href='index.php'>Tillbaka</a><p>Det finns ingen dag när alla tre är lediga</p>";
    }

    //Visas om det inte finns några filmer som inte är fullbokade när alla tre är lediga
    public function showNoMovies() {
        return "<a href='index.php'>Tillbaka</a><p>Det finns inga filmer som inte är fullbokade</p>";
    }

    //Visar de filmer som går när alla tre är lediga
    public function showMovies($movies) {
        $link = "<a href='index.php'>Tillbaka</a>";
        $header = "<h1>Följande filmer hittades</h1><ul>";
        $list = '';
        foreach ($movies as $movie) {
            $list .= "<li>Filmen <b>" . $movie->movie . "</b> klockan " . $movie->time . " på "
                . $movie->day . "<a href='?" . self::$dayParam . "=" . $movie->day . "&" .
                self::$timeParam . "=" . $movie->time . "'>Välj denna film och boka bord</a></li>";
        }
        $ul = "</ul>";
        $ret = $link . $header . $list . $ul;
        return $ret;
    }

}