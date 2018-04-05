<?php
require("prefsFile.php");
// créer les dossiers
define("SCREENS_DIR" ,"images/");
define("SCREENS_URL","images/");
define("SOURCES_DIR","images_sources/");
define("SOURCES_URL","images_sources/");

@mkdir(SCREENS_DIR);
@mkdir(SOURCES_DIR);

// valeurs par défaut
// changer ces paramètres dans data/config.ini ou data/config.php
$url_miniature_stream = "";
$max_images = 16;
$twitterMessages = array();
$debug = false;
$html_title_gestion = "";

if(file_exists("data/config.ini")) {
	extract(parse_ini_file("data/config.ini"));
}
if(file_exists("data/config.php")) {
	include("data/config.php");
}

if(!defined("DEBUG")) {
	define("DEBUG",$debug);
}
define("IMAGE_FORMAT","screen_%s.%s");
define("SOURCES_GLOB",SOURCES_DIR."image_*");

if($debug) {
	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL);
}

$prefs = new PrefsManager($max_images);
$Nscreens = $prefs->screenCount();

$url_miniature_stream = $prefs->get("url_miniature_stream",$url_miniature_stream);
$twitterMessages = $prefs->twitterMessages;

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
