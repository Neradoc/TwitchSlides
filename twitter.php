<?php
function twitterImage($urlImage) {
	global $iftMakerKey,$iftRebusChannel;
	$iftUrl = "https://maker.ifttt.com/trigger/$iftRebusChannel/with/key/$iftMakerKey";

	$data = ["value1" => $urlImage, "value2" => ""];
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
