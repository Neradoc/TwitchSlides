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

foreach(["down_score","up_score"] as $bouton) {
	if(isset($_POST[$bouton]) && $_POST[$bouton]!="") {
		$card = formate_card($_POST['scorecard_nom']);
		$value = intval($_POST[$bouton]);
		if($card && isset($prefs->scores[$card])) {
			$prefs->scores[$card]['score'] = $value;
			$prefs->scores[$card]['stamp'] = time();
			$prefs->save();
		}
		exit_redirect();
	}
}

if(isset($_POST["ajout_score"]) && $_POST["ajout_score"]!="") {
	$card = formate_card($_POST['scorecard_nom']);
	$value = intval($_POST["changer_score"]);
	$delta = intval($_POST["ajout_score"]);
	if($delta) $value += $delta;
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
	<form action="<?=$thisurl?>" name="scorecard" method="POST">
	Ajouter&nbsp;: <input class="new_scorecard" name="new_scorecard" value="" title="Entrer un nouveau nom et valider avec entrée"/>
	</form>
	<form action="<?=$thisurl?>" name="scorecard" method="POST">
	<?php
	if($prefs->get("scoreboard_on",false)) {
		?><button class="scoreboard_switch scoreboard_switch_on" name="scoreboard_switch" value="0" title="Activé, cliquer pour désactiver l'affichage des scores">ON</button><?
	} else {
		?><button class="scoreboard_switch scoreboard_switch_off" name="scoreboard_switch" value="1" title="Désactivé, cliquer pour activer l'affichage des scores">OFF</button><?
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
		<form action="<?=$thisurl?>" name="scorecard" method="POST">
		<p class="scorecard_line <?=$parite?>">
			<input type="submit" style="display:none" name="scorecard_ok" value="ok"/>
			<input type="hidden" name="scorecard_nom" value="<?=$card['nom']?>"/>
			<button class="rond down_score" name="down_score" value="<?=$card['score']-1?>" title="-1"><img src="cjs/bouton_moins.png"/></button>
			<button class="rond up_score" name="up_score" value="<?=$card['score']+1?>" title="+1"><img src="cjs/bouton_plus.png"/></button>
			<input class="score" type="text" name="changer_score" value="<?=$card['score']?>" title="Valeur de score, appuyer sur entrée pour modifier"/> <input class="ajout_score" type="text" name="ajout_score" value="" placeholder="+0" title="Entrer une valeur pour l'ajouter au score"/>
			<!-- <button class="rond valider_score" name="valider_score" value=""><img src="cjs/bouton_check.png"/></button> -->
			<span class="nom"><?=ucfirst($card['nom'])?></span>
			<button class="rond effacer_score" name="effacer_score" value="<?=$card['nom']?>"><img src="cjs/bouton_croix.png" title="Retirer des scores"/></button>
		</p>
		</form>
		<?
	}
	?>
	</div>
	</div>
	<?
}
