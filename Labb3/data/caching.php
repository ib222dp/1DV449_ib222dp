<?php
//https://www.virendrachandak.com/techtalk/php-isset-vs-empty-vs-is_null/
if (isset($_POST['json']) && !empty($_POST['json']) && !is_null($_POST['json'])) {
    //Kollar att indata är ett json-objekt
    //http://stackoverflow.com/questions/6041741/fastest-way-to-check-if-a-string-is-json-in-php
    if (is_object(json_decode($_POST['json']))) {
        //Filtrerar data innan det skrivs till filen (https://www.html5andbeyond.com/jquery-ajax-json-php/)
        $messages = htmlspecialchars($_POST['json'], ENT_NOQUOTES);
        $url = '../data/SRInfo.json';
        $file = file_get_contents($url);
        $data = json_decode($file);
        unset($data);
        file_put_contents($url, $messages);
    } else {
        exit();
    }
} else {
    exit();
}