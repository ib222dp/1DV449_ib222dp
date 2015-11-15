<?php

class DAO {

    //Konstruktor
    public function __construct() {

    }

    public function startCURL($url) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_USERAGENT, "ib222dp");
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        return $ch;
    }

    //Hämtar data och redirect-länk via curl
    public function getDataAndURL($url) {
        $ch = $this->startCURL($url);
        $data = curl_exec($ch);
        $redirectURL = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
        curl_close($ch);
        return array($data, $redirectURL);
    }

    //Postar formulärdata och returnerar svaret
    public function postData($url, $postFields) {
        $ch = $this->startCURL($url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postFields));
        $data = curl_exec($ch);
        curl_close($ch);
        return $data;
    }

}