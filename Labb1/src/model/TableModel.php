<?php

class TableModel extends Model
{
    //Hämtar tider för de bord som är lediga tidigast 2 timmar efter den valda filmen börjar
    public function getTableTimes($day, $time){
        $dinnerURL = $this->getMenuLink(2);
        libxml_use_internal_errors(true);
        $xpath = $this->getResponse($dinnerURL, false);
        //Hämtar de bord som är lediga den valda dagen
        if(strcasecmp($day, "Fredag") == 0) {
            $section = $xpath->query("//div[@class = 'WordSection2']//p//input");
        } elseif(strcasecmp($day, "Lördag" == 0)) {
            $section =  $xpath->query("//div[@class = 'WordSection4']//p//input");
        } else {
            $section =  $xpath->query("//div[@class = 'WordSection6']//p//input");
        }
        //Lägger till de bord som är lediga tidigast 2 timmar efter den valda filmen börjar
        $timeArray = array();
        $earliestTime = date('H',strtotime('+2 hours', strtotime($time)));
        foreach($section as $input) {
            $bTime = substr($input->getAttribute("value"), 3, -2);
            if((int)$bTime >= (int)$earliestTime) {
                array_push($timeArray, $bTime);
            }
        }
        foreach($timeArray as &$tTime) {
            $endTime = (int)$tTime + 2;
            $tTime = $tTime . "-" . $endTime;
        }
        return $timeArray;
    }

}
