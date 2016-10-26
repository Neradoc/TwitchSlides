<?php
require_once('twitteroauth/autoload.php');
use Abraham\TwitterOAuth\TwitterOAuth;
if(!isset($twitterUtiliserApi)) $twitterUtiliserApi = false;

function twitterImageAPI($imageFile) {
	global $twitterConsumerKey, $twitterConsumerSecret, $twitterAccessToken, $twitterAccessTokenSecret;
	$connection = new TwitterOAuth($twitterConsumerKey, $twitterConsumerSecret, $twitterAccessToken, $twitterAccessTokenSecret);

	// message du tweet
	$tweetMessage = "Venez décrypter le rébus avec nous sur le stream\nhttps://www.twitch.tv/realmyop2";
	// ajout de l'image
	$media = $connection->upload('media/upload', ['media' => SCREENS_DIR.$imageFile]);
	// zou
	$parameters = [
		'status' => $tweetMessage,
		'media_ids' => $media->media_id_string,
	];
	$result = $connection->post('statuses/update', $parameters);
	//file_put_contents("log.".time().".".uniqid().".log",json_encode($result));
	return true;
}

function twitterImageIfttt($imageFile) {
	global $iftMakerKey,$iftRebusChannel;
	$iftUrl = "https://maker.ifttt.com/trigger/$iftRebusChannel/with/key/$iftMakerKey";

	$urlImage = dirname($thisurl).SCREENS_URL.$file;
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

function twitterImage($imageFile) {
	global $twitterUtiliserApi;
	// TODO: tester la présence et les valeurs des configs ifttt et/ou api
	if($twitterUtiliserApi) {
		return twitterImageAPI($imageFile);
	} else {
		return twitterImageIfttt($imageFile);
	}
}

if(isset($_POST["twitter_screen"])) {
	$screen = intval($_POST["twitter_screen"]);
	$imageFile = $prefs->screenFile($screen);
	if($imageFile && file_exists(SCREENS_DIR.$imageFile)) {
		if(!in_array($imageFile,$prefs->tweets)) {
			$res = twitterImage($imageFile);
			if($res) {
				$prefs->tweets[] = $imageFile;
				$prefs->save();
			}
		}
	}
	exit_redirect();
}
