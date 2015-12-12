<?php
//https://www.html5andbeyond.com/jquery-ajax-json-php/
//http://stackoverflow.com/questions/6041741/fastest-way-to-check-if-a-string-is-json-in-php
if(isset($_POST['json']) {
if(is_object(json_decode($_POST['json']) {
$messages = htmlspecialchars($_POST['json'], ENT_NOQUOTES);
$file = file_get_contents('../json/SRInfo.json');
$data = json_decode($file);
unset($data);
file_put_contents('../json/SRInfo.json', $messages);
} else {
exit();
}
} else {
exit();
}
