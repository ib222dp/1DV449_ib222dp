<?php
$messages = $_POST['json'];
$file = file_get_contents('../json/SRInfo.json');
$data = json_decode($file);
unset($data);
file_put_contents('../json/SRInfo.json', $messages);