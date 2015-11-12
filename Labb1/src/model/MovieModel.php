<?php

class MovieModel extends Model
{

    public function getFriends() {
        $calendarURL = $this->getMenuLink(0);
        $xpath = $this->getXPath($calendarURL);
        $friends = $xpath->query('//a');
        return $friends;
    }


    public function getDayLists($friends) {
        $calendarURL = $this->getMenuLink(0);
        $dayLists = array();
        foreach($friends as $friend) {
            $url = $calendarURL . $friend->getAttribute("href");
            $xpath = $this->getXPath($url);
            $dayList = $xpath->query('//td');
            array_push($dayLists, $dayList);
        }
        return $dayLists;
    }


    public function calculateMovieDays($dayLists) {
        $availArray = array();
        $ok = "ok";
        foreach($dayLists as $dayList) {
            $arrLength = $dayList->length;
            for ($i = 0; $i < $arrLength; $i++) {
                $availability = $dayList[$i]->nodeValue;
                if ($i == 0) {
                    if (strcasecmp($ok, $availability) == 0) {
                        $avail = "fri";
                    } else {
                        $avail = "no";
                    }
                }
                if ($i == 1) {
                    if (strcasecmp($ok, $availability) == 0) {
                        $avail = "sat";
                    } else {
                        $avail = "no";
                    }
                }
                if ($i == 2) {
                    if (strcasecmp($ok, $availability) == 0) {
                        $avail = "sun";
                    } else {
                        $avail = "no";
                    }
                }
                array_push($availArray, $avail);
            }
        }
        $count = array_count_values($availArray);
        $movieDays = array();
        if($count["fri"] == 3){
            $friday = "Fredag";
            array_push($movieDays, $friday);
        } if($count["sat"] == 3) {
            $saturday = "Lördag";
            array_push($movieDays, $saturday);
        } if($count["sun"] == 3) {
            $sunday = "Söndag";
            array_push($movieDays, $sunday);
        }
        return $movieDays;
    }


    public function getMovies($movieDays) {
        $cinemaURL = $this->getMenuLink(1);
        $xpath = $this->getXPath($cinemaURL);
        $dayOptions = $xpath->query('//select[@name = "day"]//option');
        $movieOptions = $xpath->query('//select[@name = "movie"]//option[@value]');
        $movies = array();
        //unset($movieDays);
        //$movieDays = array("Fredag", "Lördag", "Söndag");
        foreach($movieDays as $movieDay) {
            foreach ($dayOptions as $dayOption) {
                if (strcasecmp($dayOption->nodeValue, $movieDay) == 0) {
                    foreach ($movieOptions as $movieOption) {
                        $jsonURL = $cinemaURL . "/check?day=" . $dayOption->getAttribute("value") .
                            "&movie=" . $movieOption->getAttribute("value");
                        $pageAndURL = $this->getPageAndURL($jsonURL);
                        $jsonObjects = json_decode($pageAndURL[0]);
                        foreach($jsonObjects as $movie) {
                            if ($movie->status == 1) {
                                $movie->day = $movieDay;
                                array_push($movies, $movie);
                            }
                        }
                    }
                }
            }
        }
        foreach($movies as $movie) {
            foreach ($movieOptions as $movieOpt) {
                if (strcasecmp($movieOpt->getAttribute("value"), $movie->movie) == 0) {
                    $movie->movie = $movieOpt->nodeValue;
                }
            }
        }
        return $movies;
    }

}
