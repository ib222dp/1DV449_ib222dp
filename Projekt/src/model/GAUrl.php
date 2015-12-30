<?php

require_once("EurKey.php");

class GAUrl {

    private $EurKey;
    private $start;
    private $end;
    private $query;
    private $authQuery;
    private $authQF;
    private $year;
    private $lang;

    public function __construct() {
        $this->EurKey = new EurKey();
        $this->start = 'http://www.europeana.eu/api/v2/search.json?wskey=' . $this->EurKey->getValue();
        $this->end = '&qf=europeana_collectionName:"9200365_Ag_EU_TEL_a0142_Gallica"&qf=TYPE:"TEXT"&profile=minimal';
        //media=true
        $this->query = '&query=';
        $this->authQuery = '&query=who:';
        $this->authQF = '&qf=who:';
        $this->year = '&qf=YEAR:';
        $this->lang = '&qf=proxy_dc_language:';
    }

    public function getStart() {
        return $this->start;
    }

    public function getEnd() {
        return $this->end;
    }

    public function getQuery() {
        return $this->query;
    }

    public function getAuthQuery() {
        return $this->authQuery;
    }

    public function getAuthQF() {
        return $this->authQF;
    }

    public function getYear() {
        return $this->year;
    }

    public function getLang() {
        return $this->lang;
    }

}