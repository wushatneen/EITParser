<?php
header('Content-type: text/xml');
//error_reporting(E_ALL);
include_once 'autoload.php';
if($_GET['id']>=10) {
	$id = $_GET['id']%10;
	$page = substr($_GET['id'],0, -1);
	$page++;
	
} else {
	$page = 1;
	$id = $_GET['id'];
}

define("URL",'http://url/page/'.$page);

if($_GET['type'] == 'short'){ 
	$obj = new getShortInfoById(URL);
} elseif ($_GET['type'] == 'full') {
	$obj = new getFullInfoById(URL);
} elseif ($_GET['type'] == 'best') {
	$obj = new getBestOfMonth(URL);
}

print $obj->get_xml($id);
