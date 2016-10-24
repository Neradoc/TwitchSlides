<?
if(isset($_SERVER['REDIRECT_rewrite'])) {
	$file = $_SERVER['REDIRECT_rewrite'];
	$file = preg_replace('/\.php$/','',$file);
	$file = preg_replace('/[^a-z0-9_-]/i','',$file);
	$file = "app/main_".$file.".php";
	if(file_exists($file)) {
		require($file);
	} else {
		header("HTTP/1.0 404 Not Found");
	}
} else {
	include("app/main_slide.php");
}
