<?php

class View
{
    private $model;
    private $urlForm;

    //Konstruktor
    public function __construct(Model $model) {
        $this->model = $model;

        $this->urlForm .= "
                            <form action='index.php' method='post'>
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