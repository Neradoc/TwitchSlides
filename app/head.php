<?php
// créer les dossiers
define("SCREENS_DIR" ,"images/");
define("SCREENS_URL","images/");
define("SOURCES_DIR","sources/");
define("SOURCES_URL","sources/");

@mkdir(SCREENS_DIR);
@mkdir(SOURCES_DIR);

// valeurs par défaut
// changer ces paramètres dans config_user.php
$url_miniature_stream = "";
$Nscreens = 1;
$debug = false;

if(file_exists("data/config.ini")) {
	extract(parse_ini_file("data/config.ini"));
}
if(file_exists("data/config.php")) {
	include("data/config.php");
}

if(!defined("DEBUG")) {
	define("DEBUG",$debug);
}
define("IMAGE_FORMAT","screen_%s_%d.%s");
define("SOURCES_GLOB",SOURCES_DIR."image_*");

function thisurl($params = []) {
	$thisurl = 'http';
	if(isset($_SERVER['HTTPS'])) $thisurl .= 's';
	$thisurl .= '://';
	$thisurl .= $_SERVER['HTTP_HOST'];
	$thisurl .= $_SERVER['REQUEST_URI'];
	foreach($params as $park => $parv) {
		if(preg_match('/([?&])'.$park.'=[^&]+/',$thisurl)) {
			$thisurl = preg_replace('/([?&])'.$park.'=[^&]+/', '\1'.$park.'='.$parv,$thisurl);
		} else {
			if(preg_match('/\?/',$thisurl)) {
				$thisurl .= "&";
			} else {
				$thisurl .= "?";
			}
			$thisurl .= $park.'='.$parv;
		}
	}
	return $thisurl;
}
$thisurl = thisurl();

function exit_redirect($debug = DEBUG) {
	global $thisurl;
	if($debug) {
		print("<pre>");
		print_r($_POST);
		print('<a href="'.$thisurl.'">'.$thisurl.'</a>');
	} else {
		header('Location: '.$thisurl);
		//header("Refresh:0");
	}
	exit();
}

function intag($texte) {
	return htmlspecialchars(strip_tags($texte),ENT_QUOTES);
}
