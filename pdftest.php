<?php
$res = array();
$url = 'http://beta.easyordertablethrows.com';
$file_name = '/home/easyordertt/beta.easyordertablethrows.com/public/testpdf.pdf';
$file_path = $file_name;		

if($_SERVER['SERVER_NAME']=='192.168.1.77'){
	$output	= exec("phantomjs pages.js  $url $file_path 2>&1"); 
}
else{
	$exe = "phantomjs/bin/phantomjs";
	$output = exec("$exe --ssl-protocol=any --ignore-ssl-errors=yes pages.js  $url $file_path 2>&1");
}

if($output){
	$res = array('file_name'=>$file_name);
}else{
	$res = array('file_name'=>'');
}

header('Content-type: application/pdf');
header('Content-Disposition: inline; filename="' . $filename . '"');
header('Content-Transfer-Encoding: binary');
header('Accept-Ranges: bytes');
echo file_get_contents($file_path);
die();

