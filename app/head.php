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

if(file_exists("config.php")) {
	include("config.php");
}

define("IMAGE_FORMAT","screen_%s.%s");
define("SOURCES_GLOB",SOURCES_DIR."image_*");

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
function remplace_accents($string) 
{
	return str_replace( array('à','á','â','ã','ä', 'ç', 'è','é','ê','ë', 'ì','í','î','ï', 'ñ', 'ò','ó','ô','õ','ö', 'ù','ú','û','ü', 'ý','ÿ', 'À','Á','Â','Ã','Ä', 'Ç', 'È','É','Ê','Ë', 'Ì','Í','Î','Ï', 'Ñ', 'Ò','Ó','Ô','Õ','Ö', 'Ù','Ú','Û','Ü', 'Ý'), array('a','a','a','a','a', 'c', 'e','e','e','e', 'i','i','i','i', 'n', 'o','o','o','o','o', 'u','u','u','u', 'y','y', 'A','A','A','A','A', 'C', 'E','E','E','E', 'I','I','I','I', 'N', 'O','O','O','O','O', 'U','U','U','U', 'Y'), $string); 
}
