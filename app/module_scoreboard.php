<?php

function formate_card($post) {
	return preg_replace('/[^-_a-zA-Z0-9 \']/','', remplace_accents($post));
}

if(isset($_POST['effacer_score'])) {
	$card = formate_card($_POST['scorecard_nom']);
	if($card && isset($prefs->scores[$card])) {
		unset($prefs->scores[$card]);
		$prefs->save();
	}
	exit_redirect();
}

foreach(["down_score","up_score","changer_score"] as $bouton) {
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
	$scores = $prefs->scores;
	usort($scores,function($a,$b) {
		if($b['score'] == $a['score'])
			return strcmp($a['nom'],$b['nom']);
		return $b['score'] - $a['score'];
	});
	?>
	<div id="scoreboard">
	<form action="<?=$thisurl?>" name="scorecard" method="POST">
	<h3>Scores</h3>
	Ajouter&nbsp;: <input class="new_scorecard" name="new_scorecard" value=""/>
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
			<button name="down_score" value="<?=$card['score']-1?>"><img src="cjs/bouton_moins.png"/></button>
			<button name="up_score" value="<?=$card['score']+1?>"><img src="cjs/bouton_plus.png"/></button>
			<input class="score" type="number" name="changer_score" value="<?=$card['score']?>"/>
			<button name="valider_score" value=""><img src="cjs/bouton_check.png"/></button>
			<span class="nom"><?=ucfirst($card['nom'])?></span>
			<button class="effacer_score" name="effacer_score" value="<?=$card['nom']?>"><img src="cjs/bouton_croix.png"/></button>
		</p>
		</form>
		<?
	}
	?>
	</div>
	</div>
	<?
}