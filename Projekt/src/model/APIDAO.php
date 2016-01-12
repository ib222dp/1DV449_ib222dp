<?php

class APIDAO {

    public function __construct() {

    }

    public function startCURL($url) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        return $ch;
    }

    public function getData($url) {
        $ch = $this->startCURL($url);
        $data = curl_exec($ch);
        if($data === false) {
            curl_close($ch);
            return null;
        } else {
            curl_close($ch);
            return $data;
        }
    }

}