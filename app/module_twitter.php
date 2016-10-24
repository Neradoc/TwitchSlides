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
	return preg_match('/^Congratulations!/',$response);
}

if(isset($_POST["twitter_screen"])) {
	$screen = intval($_POST["twitter_screen"]);
	$file = $prefs->screenFile($screen);
	if($file && file_exists(SCREENS_DIR.$file)) {
		$urlImage = dirname($thisurl).SCREENS_URL.$file;
		if(!in_array($urlImage,$prefs->tweets)) {
			$res = twitterImage($urlImage);
			if($res) {
				$prefs->tweets[] = $urlImage;
				$prefs->save();
			}
		}
	}
	exit_redirect(true);
}
