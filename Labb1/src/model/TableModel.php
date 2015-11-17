<?php

require_once("Table.php");

class TableModel extends Model
{
    public function getDinnerXPath() {
        $dinnerURL = $this->getMenuLink(2);
        libxml_use_internal_errors(true);
        $xpath = $this->getResponse($dinnerURL, false);
        return $xpath;
    }

    public function getAttr($list, $attr) {
        $attribute = '';
        foreach($list as $item) {
            $attribute = $item->getAttribute($attr);
        }
        return $attribute;
    }

    //Hämtar tider för de bord som är lediga tidigast 2 timmar efter den valda filmen börjar
    public function getTables($day, $time){
        $xpath = $this->getDinnerXPath();
        //Hämtar de bord som är lediga den valda dagen
        if(strcasecmp($day, "fredag") == 0) {
            $section = $xpath->query("//div[@class = 'WordSection2']//p//input");
        } elseif(strcasecmp($day, "lördag" == 0)) {
            $section =  $xpath->query("//div[@class = 'WordSection4']//p//input");
        } else {
            $section =  $xpath->query("//div[@class = 'WordSection6']//p//input");
        }
        //Lägger till de bord som är lediga tidigast 2 timmar efter den valda filmen börjar
        $tables = array();
        $earliestTime = date('H',strtotime('+2 hours', strtotime($time)));
        foreach($section as $input) {
            $startTime = substr($input->getAttribute("value"), 3, -2);
            if((int)$startTime >= (int)$earliestTime) {
                $endTime = substr($input->getAttribute("value"), -2);
                $tableTime = $startTime . "-" . $endTime;
                $table = new Table($tableTime, $input->getAttribute("value"));
                array_push($tables, $table);
            }
        }
        return $tables;
    }

    //Hämtar den url som bokningsformuläret ska postas till
    public function getFormURL () {
        $xpath = $this->getDinnerXPath();
        $forms = $xpath->query("//form");
        $url = $this->getAttr($forms, "action");
        $formURL = $this->getSavedURL() . $url;
        return $formURL;
    }

    //Returnerar en array med de värden som ska postas via bokningsformuläret
    public function getPostFields($bookTime) {
        $xpath = $this->getDinnerXPath();
        $inputFields = $xpath->query("//input[@value = '" . $bookTime . "']");
        $userNameFields = $xpath->query("//input[@type = 'text']");
        $passwordFields = $xpath->query("//input[@type = 'password']");
        $submitFields = $xpath->query("//input[@type = 'submit']");
        $inputName = $this->getAttr($inputFields, "name");
        $userName = $this->getAttr($userNameFields, "name");
        $password = $this->getAttr($passwordFields, "name");
        $submitName = $this->getAttr($submitFields, "name");
        $submitValue = $this->getAttr($submitFields, "value");
        $postFields = array($inputName => $bookTime, $userName => "zeke", $password => "coys",
           $submitName => $submitValue);
        return $postFields;
    }

}
