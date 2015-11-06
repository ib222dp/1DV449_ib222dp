<?php

class Model
{

    public function getPage($url) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        $data = curl_exec($ch);
        curl_close($ch);

        return $data;
    }

    public function loadHTML($page) {
        $dom = new DOMDocument();

        if($dom->loadHTML($page)){
            $xpath = new DOMXPath($dom);
            return $xpath;
        } else {
            die("Fel");
        }
    }

    public function getMenuLinks($page) {

        $xpath = $this->loadHTML($page);

        $menuLinks = $xpath->query('//a');

        return $menuLinks;
    }

    public function getFriendLinks($menuLinks) {

        $calendarLink = $menuLinks->item(0);

        $url = $_SESSION["givenURL"] . $calendarLink->getAttribute("href") . "/";

        $page = $this->getPage($url);

        $xpath = $this->loadHTML($page);

        $friendLinks = $xpath->query('//a');

        return array($friendLinks, $url);
    }

    public function getFriendDates($friendArray) {

        $frDatesArray = array();

        foreach($friendArray[0] as $friend) {
            $url = $friendArray[1] . $friend->getAttribute("href");
            $page = $this->getPage($url);
            $xpath = $this->loadHTML($page);
            $friendDates = $xpath->query('//td');
            array_push($frDatesArray, $friendDates);
        }

        foreach($frDatesArray[0] as $paul) {
            $availability = $paul->nodeValue;
        }


    }

}
