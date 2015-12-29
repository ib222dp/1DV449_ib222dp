<?php

require_once("DAL.php");
require_once("BHLKey.php");
require_once("EurKey.php");

class SearchModel
{
    private $dal;
    private $BHLKey;
    private $EurKey;

    //Konstruktor
    public function __construct() {
        $this->dal = new DAL();
        $this->BHLKey = new BHLKey();
        $this->EurKey = new EurKey();
    }

    //Förstör sessionen
    public function destroySession() {
        $_SESSION = array();
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $params["path"],
                $params["domain"], $params["secure"], $params["httponly"]);
        }
        session_destroy();
    }

    public function getGallicaResults($param) {
        //$url = 'http://www.europeana.eu/api/v2/search.json?wskey=' . $this->EurKey->getValue() . '&query=title:' . $param .
        //  '&qf=europeana_collectionName:"9200365_Ag_EU_TEL_a0142_Gallica"&qf=TYPE:"TEXT"&profile=minimal';
        //media=true
        //$data = $this->dal->getData($url);
        //$results = json_decode($data);
        //$items = $results->items;
        $fileUrl = __DIR__ . '/results.json';
        $fileContents = file_get_contents($fileUrl);
        $oldResults = json_decode($fileContents);
        //unset($oldResults);
        //file_put_contents($fileUrl, json_encode($items));
        //return $items;
        return $oldResults;
    }

    public function getBHLResults($param) {
        //$url = 'http://www.biodiversitylibrary.org/api2/httpquery.ashx?op=BookSearch&title=' . $param .
        //        '&apikey=' . $this->BHLKey->getValue() . '&format=json';
        //$data = $this->dal->getData($url);
        //$results = json_decode($data);
        //$items = $results->Result;
        $fileUrl = __DIR__ . '/bhlresults.json';
        $fileContents = file_get_contents($fileUrl);
        $oldResults = json_decode($fileContents);
        //unset($oldResults);
        //file_put_contents($fileUrl, json_encode($items));
        //return $items;
        return $oldResults;
    }

}