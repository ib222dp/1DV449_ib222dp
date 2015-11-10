<?php

class Model
{
    public function destroySession() {
        $_SESSION = array();

        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $params["path"], $params["domain"], $params["secure"], $params["httponly"]);
        }
        session_destroy();
    }

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
        return $frDatesArray;
    }

    public function getFriendMovieDays($frDatesArray) {

        $availArray = array();

        $ok = "ok";

        foreach($frDatesArray as $friendDates) {

            $arrLength = $friendDates->length;

            for ($i = 0; $i < $arrLength; $i++) {

                $availability = $friendDates[$i]->nodeValue;

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

        $movieLink = $menuLinks->item(1);

        $url = $_SESSION["givenURL"] . $movieLink->getAttribute("href") . "/";

        $page = $this->getPage($url);

        $xpath = $this->loadHTML($page);

        $daySelect = $xpath->query('//select[@name = "day"]//option');

        $movieSelect = $xpath->query('//select[@name = "movie"]//option[@value]');

        $jsonArray = array();

        //unset($friendMovieDays);
        //$friendMovieDays = array("Fredag", "Lördag", "Söndag");

        foreach($friendMovieDays as $friendDay) {
            foreach ($daySelect as $movieDay) {
                if (strcasecmp($movieDay->nodeValue, $friendDay) == 0) {
                    foreach ($movieSelect as $movieOpt) {

                        $jsonURL = $url."check?day=".$movieDay->getAttribute("value").
                            "&movie=".$movieOpt->getAttribute("value");

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

    public function getTable($day, $time){

        $earliestTime = date('H',strtotime('+2 hours', strtotime($time)));

        $page = $this->getPage($_SESSION["givenURL"]);

        $menuLinks = $this->getMenuLinks($page);

        $tableURL = $menuLinks->item(2);

        $tableLink = $_SESSION["givenURL"] . $tableURL->getAttribute("href") . "/";

        $tablePage = $this->getPage($tableLink);

        libxml_use_internal_errors(true);

        $xpath = $this->loadHTML($tablePage);

        if(strcasecmp($day, "Fredag") == 0) {
            $section = $xpath->query("//div[@class = 'WordSection2']//p//input");
        } elseif(strcasecmp($day, "Lördag" == 0)) {
            $section =  $xpath->query("//div[@class = 'WordSection4']//p//input");
        } else {
            $section =  $xpath->query("//div[@class = 'WordSection6']//p//input");
        }

        $timeArray = array();

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
