<?php
function formate_card($post) {
	return preg_replace('/[^-_a-zA-Z0-9 \'' .'àáâãäçèéêëìíîï' .'ñòóôõöùúûüýÿ' .'ÀÁÂÃÄÇÈÉÊËÌÍÎÏ' .'ÑÒÓÔÕÖÙÚÛÜÝ]/ui','', $post);
}

if(isset($_POST['scoreboard_reset'])) {
	$prefs->scores = [];
	$prefs->save();
	exit_redirect();
}

if(isset($_POST['scoreboard_effacer'])) {
	$card = formate_card($_POST['scoreboard_nom']);
	if($card && isset($prefs->scores[$card])) {
		unset($prefs->scores[$card]);
		$prefs->save();
	}
	exit_redirect();
}

if(isset($_POST['scoreboard_index'])) {
	$prefs->set("scoreboard_index",intval($_POST['scoreboard_index'],0));
	$prefs->save();
	exit_redirect();
}

if(isset($_POST['scoreboard_switch'])) {
	$prefs->set("scoreboard_on",$_POST['scoreboard_switch']?true:false);
	$prefs->save();
	exit_redirect();
}

if(isset($_POST["scoreboard_updown"])) {
	$value = intval($_POST["scoreboard_changer"]);
	$delta = intval($_POST["scoreboard_updown"]) * intval($_POST["scoreboard_modif"]);
	if($delta != 0) {
		$card = formate_card($_POST['scoreboard_nom']);
		if($card && isset($prefs->scores[$card])) {
			$prefs->scores[$card]['score'] = $value+$delta;
			$prefs->scores[$card]['stamp'] = time();
			$prefs->save();
		}
	}
	exit_redirect();
}

if(isset($_POST['scoreboard_add'])) {
	$value = intval($_POST["scoreboard_changer"]);
	$delta = intval($_POST["scoreboard_add"]);
	if($delta != 0) {
		$card = formate_card($_POST['scoreboard_nom']);
		if($card && isset($prefs->scores[$card])) {
			$prefs->scores[$card]['score'] = $value+$delta;
			$prefs->scores[$card]['stamp'] = time();
			$prefs->save();
		}
	}
	exit_redirect();
}

if(isset($_POST["scoreboard_modif"]) && $_POST["scoreboard_modif"]!="") {
	$card = formate_card($_POST['scoreboard_nom']);
	$value = intval($_POST["scoreboard_changer"]);
	if($card && isset($prefs->scores[$card])) {
		$prefs->scores[$card]['score'] = $value;
		$prefs->scores[$card]['stamp'] = time();
		$prefs->save();
	}
	exit_redirect();
}

if(isset($_POST['scoreboard_new_nom']) && $_POST['scoreboard_new_nom']!="") {
	$nom = formate_card($_POST['scoreboard_new_nom']);
	if($_POST["scoreboard_new_score"] == "") $value = 1;
	else $value = intval($_POST["scoreboard_new_score"]);
	if($nom && !isset($prefs->scores[$nom])) {
		$prefs->scores[$nom] = array(
			'nom' => $nom,
			'score' => $value,
			't0' => time(),
			'stamp' => time(), // dernier gain
		);
		$prefs->save();
	}
	exit_redirect();
}

function disp_scoreboard($thisurl) {
	global $prefs,$Nscreens;
	$scores = $prefs->sortedScores();
	?>
	<div id="scoreboard" class="module_box">
	<h3>Scores</h3>
	<form action="<?=$thisurl?>" name="scoreboard_indexing" method="POST">
		<?php $zindex = intval($prefs->get('scoreboard_index',0)); ?>
		<select class="scoreboard_index" name="scoreboard_index" title="Position par rapport aux écrans">
			<option value="0">Derrière</option><?php
			for($screen=1; $screen<$Nscreens; $screen++) {
				$selected = $screen == $zindex ? "selected" : "";
				?><option value="<?=$screen?>" <?=$selected?>>Entre <?=$screen ?> et <?=$screen+1 ?></option><?
			}
			$selected = ($Nscreens == $zindex || $zindex == 100) ? "selected" : ""; ?>
			<option value="100" <?=$selected?>>Devant</option>
		</select>
	</form>
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
		<div class="scoreboard_line <?=$parite?>">
		<form action="<?=$thisurl?>" name="scorecard" method="POST" autocomplete="off">
			<input type="submit" style="display:none" name="scoreboard_ok" value="ok"/>
			<input type="hidden" name="scoreboard_nom" value="<?=$card['nom']?>"/>
			<button class="btn_image down_score" name="scoreboard_updown" value="-1" title="Réduire le score du nombre indiqué"><img src="cjs/img/bouton_moins.png"/></button>
			<input class="modif_score" type="text" name="scoreboard_modif" value="1" title="Valeur pour modifier le score"/>
			<button class="btn_image up_score" name="scoreboard_updown" value="1" title="Augmenter le score du nombre indiqué"><img src="cjs/img/bouton_plus.png"/></button>
			<?php if($prefs->active_screen()!==null): ?>
			<button class="btn_image active_score score_value" name="scoreboard_add" value="0"><img src="cjs/img/icone-scoring.png" title="Donner les points du jeu en cours"/></button>
			<?php endif; ?>
			<input class="score" type="text" name="scoreboard_changer" value="<?=$card['score']?>" title="Valeur de score, appuyer sur entrée pour modifier"/>
			<!-- <button class="btn_image valider_score" name="scoreboard_valider" value=""><img src="cjs/img/bouton_check.png"/></button> -->
			<span class="nom"><?=$card['nom']?></span>
			<button class="btn_image effacer_score" name="scoreboard_effacer" value="<?=$card['nom']?>"><img src="cjs/img/bouton_croix.png" title="Retirer le score"/></button>
		</form>
		</div>
		<?
	}
	?>
	</div>
	<form action="<?=$thisurl?>" name="scoreboard" method="POST">
	<div class="scoreboard_new">
		Ajouter&nbsp;<input class="scoreboard_new_score" name="scoreboard_new_score" value="" title="Nouvelle valeur de points" placeholder="Score (1)"/><input class="scoreboard_new_nom" name="scoreboard_new_nom" value="" title="Entrer un nouveau nom et valider avec entrée" placeholder="Nom du nouveau"/><button class="btn_image scoreboard_new_btn" name="scoreboard_new_btn" value="ok"><img src="cjs/img/bouton_check.png"/></button>
	</div>
	</form>
	</div>
	<?
}
