<?php
// créer les dossiers
define("IMAGES_DIR" ,"../images");
define("IMAGES_URL","images");
define("SOURCES_DIR","../sources");
define("SOURCES_URL","sources");

@mkdir(IMAGE_DIR);
@mkdir(SOURCES_DIR);

// valeurs par défaut
// changer ces paramètres dans config_user.php
$url_miniature_stream = "";
$Nscreens = 1;

if(file_exists("config.php")) {
	include("config.php");
}

$image_format = IMAGES_DIR."/screen_%s.%s";
$sources_glob = "../".SOURCES_DIR."/image_*";

$thisurl = 'http';
if(isset($_SERVER['HTTPS'])) $thisurl .= 's';
$thisurl .= '://';
$thisurl .= $_SERVER['HTTP_HOST'];
$thisurl .= $_SERVER['REQUEST_URI'];

if(!defined("DEBUG")) {
	define("DEBUG",false);
}
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
