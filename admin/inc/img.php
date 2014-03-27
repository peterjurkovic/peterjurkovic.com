<?php

ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', dirname(__FILE__).'/log.txt');
error_reporting(E_ALL);

include_once dirname(__FILE__).'/class.Image.php';	

$fileLocation =  str_replace("/admin/inc", "", dirname(__FILE__)).'/data'. $_GET['url'];
if(is_file($fileLocation)){
	$image = new Image($fileLocation);	
	$image->resizeImage($_GET['w'], $_GET['h'], $_GET["type"], false, true);
}

?>
