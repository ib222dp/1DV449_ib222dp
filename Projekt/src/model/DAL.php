<?php

class DAL {

    //Konstruktor
    public function __construct() {

    }

    public function startCURL($url) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        return $ch;
    }

    //Hämtar data och redirect-länk via curl
    public function getData($url) {
        $ch = $this->startCURL($url);
        $data = curl_exec($ch);
        if($data == false) {
            curl_close($ch);
            die("<a href='index.php'>Tillbaka</a><p>Något gick fel.</p>");
        } else {
            curl_close($ch);
            return $data;
        }
    }

}