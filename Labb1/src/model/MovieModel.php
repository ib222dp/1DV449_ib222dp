<?php

class MovieModel extends Model
{

    public function getFriendLinks($menuLinks) {
        $url = $this->buildURL($menuLinks->item(0));
        $xpath = $this->getXPath($url);
        $friendLinks = $xpath->query('//a');
        return array($friendLinks, $url);
    }


    public function getFriendDays($friendArray) {

        $dayLists = array();

        foreach($friendArray[0] as $friend) {
            $url = $friendArray[1] . $friend->getAttribute("href");
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

        $movieDayArray = array();

        if($count["fri"] == 3){
            $friday = "Fredag";
            array_push($movieDayArray, $friday);
        } if($count["sat"] == 3) {
            $saturday = "Lördag";
            array_push($movieDayArray, $saturday);
        } if($count["sun"] == 3) {
            $sunday = "Söndag";
            array_push($movieDayArray, $sunday);
        }

        return $movieDayArray;
    }


    public function getMovies($friendMovieDays, $menuLinks) {
        $url = $this->buildURL($menuLinks->item(1));
        $xpath = $this->getXPath($url);

        $daySelect = $xpath->query('//select[@name = "day"]//option');
        $movieSelect = $xpath->query('//select[@name = "movie"]//option[@value]');

        $jsonArray = array();

        //unset($friendMovieDays);
        //$friendMovieDays = array("Fredag", "Lördag", "Söndag");

        foreach($friendMovieDays as $friendDay) {
            foreach ($daySelect as $movieDay) {
                if (strcasecmp($movieDay->nodeValue, $friendDay) == 0) {
                    foreach ($movieSelect as $movieOpt) {

                        $jsonURL = $url . "check?day=" . $movieDay->getAttribute("value") .
                            "&movie=" . $movieOpt->getAttribute("value");

                        $json = $this->getPage($jsonURL);

                        $movies = json_decode($json);

                        foreach($movies as $movie) {
                            if ($movie->status == 1) {
                                $movie->day = $friendDay;
                                array_push($jsonArray, $movie);
                            }
                        }
                    }
                }
            }
        }

        foreach($jsonArray as $movie) {
            foreach ($movieSelect as $movieOption) {
                if (strcasecmp($movieOption->getAttribute("value"), $movie->movie) == 0) {
                    $movie->movie = $movieOption->nodeValue;
                }
            }
        }

        return $jsonArray;
    }

}
