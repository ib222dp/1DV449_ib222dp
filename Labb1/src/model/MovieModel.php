<?php

class MovieModel extends Model
{
    //Kollar att url har angetts
    public function inputOK($url){
        if(empty($url)){
            return false;
        }else{
            return true;
        }
    }

    //Hämtar länkar till vännernas kalendrar
    public function getFriends() {
        $calendarURL = $this->getMenuLink(0);
        $xpath = $this->getResponse($calendarURL, false);
        $friends = $xpath->query('//a');
        return $friends;
    }

    //Returnerar en array med vännernas kalenderlistor
    public function getDayLists($friends) {
        $calendarURL = $this->getMenuLink(0);
        $dayLists = array();
        foreach($friends as $friend) {
            $url = $calendarURL . $friend->getAttribute("href");
            $xpath = $this->getResponse($url, false);
            $dayList = $xpath->query('//td');
            array_push($dayLists, $dayList);
        }
        return $dayLists;
    }

    //Returnerar en array med de dagar som alla tre är lediga
    public function calculateMovieDays($dayLists) {
        $availArray = array();
        $ok = "ok";
        $fri = "fri";
        $sat = "sat";
        $sun = "sun";
        //Lägger till fri,sat eller sun i availArray beroende på om vännerna är lediga(om td=ok)
        foreach($dayLists as $dayList) {
            $arrLength = $dayList->length;
            for ($i = 0; $i < $arrLength; $i++) {
                $availability = $dayList[$i]->nodeValue;
                if ($i == 0) {
                    if (strcasecmp($ok, $availability) == 0) {
                        array_push($availArray, $fri);
                    }
                }
                if ($i == 1) {
                    if (strcasecmp($ok, $availability) == 0) {
                        array_push($availArray, $sat);
                    }
                }
                if ($i == 2) {
                    if (strcasecmp($ok, $availability) == 0) {
                        array_push($availArray, $sun);
                    }
                }
            }
        }
        //Lägger till Fredag, Lördag eller Söndag i movieDays om alla tre är lediga den dagen
        $count = array_count_values($availArray);
        $movieDays = array();
        if(in_array($fri, $availArray)) {
            if($count[$fri] == 3){
                array_push($movieDays, self::$day1);
            }
        } if(in_array($sat, $availArray)) {
            if($count[$sat] == 3) {
                array_push($movieDays, self::$day2);
            }
        } if(in_array($sun, $availArray)) {
            if($count[$sun] == 3) {
                array_push($movieDays, self::$day3);
            }
        }
        return $movieDays;
    }

    //Hämtar de filmer som går och inte är fullbokade de dagar som alla tre är lediga
    public function getMovies($movieDays) {
        $cinemaURL = $this->getMenuLink(1);
        $xpath = $this->getResponse($cinemaURL, false);
        $dayOptions = $xpath->query('//select[@name = "day"]//option');
        $movieOptions = $xpath->query('//select[@name = "movie"]//option[@value]');
        $movies = array();
        foreach($movieDays as $movieDay) {
            foreach ($dayOptions as $dayOption) {
                if (strcasecmp($dayOption->getAttribute("value"), $movieDay) == 0) {
                    foreach ($movieOptions as $movieOption) {
                        $jsonURL = $cinemaURL . "check?day=" . $dayOption->getAttribute("value") .
                            "&movie=" . $movieOption->getAttribute("value");
                        $json = $this->getResponse($jsonURL, true);
                        $jsonObjects = json_decode($json);
                        foreach($jsonObjects as $movie) {
                            if ($movie->status == 1) {
                                //Byter ut movie->movie från ett nummer till namnet på filmen
                                if (strcasecmp($movieOption->getAttribute("value"), $movie->movie) == 0) {
                                    $movie->movie = utf8_decode($movieOption->nodeValue);
                                }
                                if($movieDay == self::$day1) {
                                    $movie->day = "fredag";
                                } elseif($movieDay == self::$day2) {
                                    $movie->day = "lördag";
                                } else {
                                    $movie->day = "söndag";
                                }
                                array_push($movies, $movie);
                            }
                        }
                    }
                }
            }
        }
        return $movies;
    }

}
