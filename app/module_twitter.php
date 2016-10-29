<?php
require_once('twitteroauth/autoload.php');
use Abraham\TwitterOAuth\TwitterOAuth;
if(!isset($twitterUtiliserApi)) $twitterUtiliserApi = false;

function twitterImageAPI($imageFile,$tweetMessage) {
	global $twitterConsumerKey, $twitterConsumerSecret, $twitterAccessToken, $twitterAccessTokenSecret;
	$connection = new TwitterOAuth($twitterConsumerKey, $twitterConsumerSecret, $twitterAccessToken, $twitterAccessTokenSecret);

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

function twitterImageIfttt($imageFile,$tweetMessage) {
	global $iftMakerKey,$iftRebusChannel;
	$iftUrl = "https://maker.ifttt.com/trigger/$iftRebusChannel/with/key/$iftMakerKey";

	$urlImage = dirname(thisurl()).SCREENS_URL.$imageFile;
	$data = array("value1" => $urlImage, "value2" => $tweetMessage);
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

function twitterImage($imageFile,$tweetMessage) {
	global $twitterUtiliserApi;
	// TODO: tester la présence et les valeurs des configs ifttt et/ou api
	if($twitterUtiliserApi) {
		return twitterImageAPI($imageFile,$tweetMessage);
	} else {
		return twitterImageIfttt($imageFile,$tweetMessage);
	}
}

if(isset($_POST["twitter_screen"])) {
	$message = $_POST["twitter_message"];
	$screen = intval($_POST["twitter_screen"]);
	$imageFile = $prefs->screenFile($screen);
	if($imageFile && file_exists(SCREENS_DIR.$imageFile)) {
		if(!in_array($imageFile,$prefs->tweets)) {
			$res = twitterImage($imageFile,$message);
			if($res) {
				$prefs->tweets[] = $imageFile;
				$prefs->save();
			}
		}
	}
	exit_redirect();
}

function disp_twitter($thisurl) {
	global $messages_twitter;
	/*
	afficher la fenêtre de choix du dialogue (masqué à la base)
	quand on clique sur le bouton affiche
		- l'image pour récapituler (avec une taille limite)
		- le message, avec une liste de choix possibles
		- un bouton pour valider l'envoi (fait quelles vérifications?)
		  (compte les 140 caractères et la taille du fichier <1Mo ?)
		- prévient si ça a déjà été envoyé ?
	*/
	?>
	<div id="twitter_window">
	<form action="<?=$thisurl?>" name="twitter_en_vrai" method="POST">
		<input type="hidden" class="twitter_screen" name="twitter_screen" value=""/>
		<button class="twitter_fermer"><img src="cjs/bouton_croix.png"/></button>
		<div class="twitter_impetrant"><img src=""/></div>
		<div class="twitter_choix_message">
		<?php foreach($messages_twitter as $mess): ?>
			<div class="twitter_exemple_message" data-message="<?= intag($mess)?>"><?= nl2br(strip_tags($mess)) ?></div>
		<?php endforeach; ?>
		</div>
		<!-- <div>NOTE: dire si elle a déjà été twittée</div> -->
		<div>
			<textarea class="twitter_message" maxlength="140" name="twitter_message" placeholder="Texte du tweet. N'oubliez pas l'adresse du stream !"></textarea><br/>
			<span class="twitter_error"></span>
			<button class="twitter_envoyer">Confirmer le tweet !</button>
		</div>
	</form>
	</div><?
}
