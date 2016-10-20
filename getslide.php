<?php
include("head.php");
include("prefs.php");

$prefs = new PrefsManager();
function screenFile($screenNum) {
	global $prefs;
	if(isset($prefs->screens[$screenNum])) {
		if($prefs->screens[$screenNum] != "") {
			return "images/".$prefs->screens[$screenNum];
		}
	}
	return "";
}

$screen = false;
if(isset($_REQUEST['screen'])) {
	$screen = @intval($_REQUEST['screen']);
}
if($screen !== false) {
	$file = screenFile($screen);
	if($file && file_exists($file)) {
		print($file);
	}
}
