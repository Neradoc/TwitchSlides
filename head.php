<?php
// créer les dossiers
@mkdir("images");
@mkdir("sources");

$Nscreens = 1;

$image_format = "images/screen_%s.%s";
$sources_glob = "sources/image_*";

$thisurl = 'http';
if(isset($_SERVER['HTTPS'])) $thisurl .= 's';
$thisurl .= '://';
$thisurl .= $_SERVER['HTTP_HOST'];
$thisurl .= $_SERVER['REQUEST_URI'];

if(!defined("DEBUG")) {
	define("DEBUG",false);
}
function exit_redirect() {
	global $thisurl;
	if(DEBUG) {
		print("<pre>");
		print_r($_POST);
		print('<a href="'.$thisurl.'">'.$thisurl.'</a>');
	} else {
		header('Location: '.$thisurl);
		//header("Refresh:0");
	}
	exit();
}
