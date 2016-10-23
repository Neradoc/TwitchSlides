<?php
function twitterImage($urlImage) {
	global $iftMakerKey,$iftRebusChannel;
	$iftUrl = "https://maker.ifttt.com/trigger/$iftRebusChannel/with/key/$iftMakerKey";

	$data = array("value1" => $urlImage, "value2" => "");
	$data_string = json_encode($data);                                                                                   
	
	$ch = curl_init($iftUrl);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array(
		'Content-Type: application/json',
		'Content-Length: ' . strlen($data_string))
	);

	$response = curl_exec($ch);
	curl_close($ch);
}

if(isset($_POST["twitter_screen"])) {
	$screen = intval($_POST["twitter_screen"]);
	$file = $prefs->screenFile($screen);
	if($file && file_exists($file)) {
		$urlImage = dirname($thisurl).$file;
		if(true || !in_array($urlImage,$prefs->tweets)) {
			$prefs->tweets[] = $urlImage;
			$prefs->save();
			twitterImage($urlImage);
		}
	}
	exit_redirect(true);
}
