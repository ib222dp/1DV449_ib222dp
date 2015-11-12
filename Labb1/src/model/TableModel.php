<?php

class TableModel extends Model
{

    public function getTableTimes($day, $time){
        $dinnerURL = $this->getMenuLink(2);
        libxml_use_internal_errors(true);
        $xpath = $this->getXPath($dinnerURL);
        if(strcasecmp($day, "Fredag") == 0) {
            $section = $xpath->query("//div[@class = 'WordSection2']//p//input");
        } elseif(strcasecmp($day, "Lördag" == 0)) {
            $section =  $xpath->query("//div[@class = 'WordSection4']//p//input");
        } else {
            $section =  $xpath->query("//div[@class = 'WordSection6']//p//input");
        }
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
