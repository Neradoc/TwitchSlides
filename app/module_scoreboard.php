<?php
function formate_card($post) {
	return preg_replace('/[^-_a-zA-Z0-9 \'' .'àáâãäçèéêëìíîï' .'ñòóôõöùúûüýÿ' .'ÀÁÂÃÄÇÈÉÊËÌÍÎÏ' .'ÑÒÓÔÕÖÙÚÛÜÝ]/ui','', $post);
}

if(isset($_POST['effacer_score'])) {
	$card = formate_card($_POST['scorecard_nom']);
	if($card && isset($prefs->scores[$card])) {
		unset($prefs->scores[$card]);
		$prefs->save();
	}
	exit_redirect();
}

if(isset($_POST['scoreboard_switch'])) {
	$prefs->set("scoreboard_on",$_POST['scoreboard_switch']?true:false);
	$prefs->save();
	exit_redirect();
}

if(isset($_POST["updown_score"])) {
	$value = intval($_POST["changer_score"]);
	$delta = intval($_POST["updown_score"]) * intval($_POST["modif_score"]);
	if($delta != 0) {
		$card = formate_card($_POST['scorecard_nom']);
		if($card && isset($prefs->scores[$card])) {
			$prefs->scores[$card]['score'] = $value+$delta;
			$prefs->scores[$card]['stamp'] = time();
			$prefs->save();
		}
	}
	exit_redirect();
}

if(isset($_POST["modif_score"]) && $_POST["modif_score"]!="") {
	$card = formate_card($_POST['scorecard_nom']);
	$value = intval($_POST["changer_score"]);
	if($card && isset($prefs->scores[$card])) {
		$prefs->scores[$card]['score'] = $value;
		$prefs->scores[$card]['stamp'] = time();
		$prefs->save();
	}
	exit_redirect();
}

if(isset($_POST['new_scorecard']) && $_POST['new_scorecard']!="") {
	$card = formate_card($_POST['new_scorecard']);
	if($card && !isset($prefs->scores[$card])) {
		$prefs->scores[$card] = array(
			'nom' => $card,
			'score' => 0,
			't0' => time(),
			'stamp' => time(), // dernier gain
		);
		$prefs->save();
	}
	exit_redirect();
}

function disp_scoreboard($thisurl) {
	global $prefs;
	$scores = $prefs->sortedScores();
	?>
	<div id="scoreboard">
	<h3>Scores</h3>
	<form action="<?=$thisurl?>" name="scoreboard_onoff" method="POST">
	<?php
	if($prefs->get("scoreboard_on",false)) {
		?><button class="btn_switch btn_switch_on" name="scoreboard_switch" value="0" title="Activé, cliquer pour désactiver l'affichage des scores">ON</button><?
	} else {
		?><button class="btn_switch btn_switch_off" name="scoreboard_switch" value="1" title="Désactivé, cliquer pour activer l'affichage des scores">OFF</button><?
	}
	?>
	</form>
	<div class="scoreboard_list">
	<?
	$i = 0;
	foreach($scores as $card) {
		$i += 1;
		if($i%2) { $parite = "pair"; } else { $parite = "impair"; }
		?>
		<div class="scorecard_line <?=$parite?>">
		<form action="<?=$thisurl?>" name="scorecard" method="POST" autocomplete="off">
			<input type="submit" style="display:none" name="scorecard_ok" value="ok"/>
			<input type="hidden" name="scorecard_nom" value="<?=$card['nom']?>"/>
			<button class="rond down_score" name="updown_score" value="-1" title="Réduire le score du nombre indiqué"><img src="cjs/bouton_moins.png"/></button>
			<input class="modif_score" type="text" name="modif_score" value="1" title="Valeur pour modifier le score"/>
			<button class="rond up_score" name="updown_score" value="1" title="Augmenter le score du nombre indiqué"><img src="cjs/bouton_plus.png"/></button>
			<input class="score" type="text" name="changer_score" value="<?=$card['score']?>" title="Valeur de score, appuyer sur entrée pour modifier"/>
			<!-- <button class="rond valider_score" name="valider_score" value=""><img src="cjs/bouton_check.png"/></button> -->
			<span class="nom"><?=$card['nom']?></span>
			<button class="rond effacer_score" name="effacer_score" value="<?=$card['nom']?>"><img src="cjs/bouton_croix.png" title="Retirer le score"/></button>
		</form>
		</div>
		<?
	}
	?>
	</div>
	<form action="<?=$thisurl?>" name="scorecard" method="POST">
	Ajouter&nbsp;: <input class="new_scorecard" name="new_scorecard" value="" title="Entrer un nouveau nom et valider avec entrée"/>
	</form>
	</div>
	<?
}
