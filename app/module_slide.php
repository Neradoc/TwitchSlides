<?php
// screens
for($i=1; $i<=$Nscreens; $i++) {
	$screensNumbers[] = $i;
}

function slide_ajax() {
	global $screensNumbers,$prefs,$Nscreens;
	// ajax
	if(isset($_REQUEST['screen']) && $_REQUEST['screen']!="") {
		$screensNumbers =
			array_filter(
				array_map(
					function($x) { return intval($x); },
					preg_split('/,/', $_REQUEST['screen'], -1, PREG_SPLIT_NO_EMPTY)
				),
			function($n) use ($Nscreens) {
				return $n>0 && $n<=intval($Nscreens);
			}
		);
	}
	if(isset($_REQUEST['get'])) {
		$data = array("screens" => array());
		foreach($screensNumbers as $screen) {
			$file = $prefs->screenFile($screen);
			$pos = $prefs->screenPos($screen);
			$on = $prefs->screenOn($screen);
			if($file && file_exists(SCREENS_DIR.$file)) {
				$sizes = getimagesize(SCREENS_DIR.$file);
				$width  = $sizes[0];
				$height = $sizes[1];
				$data['screens'][] = array(
					'num' => $screen,
					'image' => SCREENS_URL.$file,
					'pos' => $pos,
					'size' => array($width,$height),
					'on' => $on?true:false,
				);
			}
		}
		// scores
		$scores = $prefs->sortedScores();
		$scoreboard_on = $prefs->get("scoreboard_on",false);
		$data['scoreboard_on'] = $scoreboard_on;
		$nscore = 0;
		$liste_scores = "";
		foreach($scores as $score) {
			$liste_scores .= "<span>".ucfirst($score['nom'])." : ".$score['score'];
			if($nscore == 0) { $liste_scores .= '<img class="crown" src="cjs/img/crown.png"/>'; }
			#if($nscore == 1) { $liste_scores .= '<img class="crown" src="cjs/img/tiare.png"/>'; }
			$liste_scores .= "</span>";
			$nscore += 1;
		}
		$data['liste_scores'] = $liste_scores;
		$zindex = intval($prefs->get('scoreboard_index',0));
		$data['scoreboard_index'] = $zindex;
		// reload
		$reload = $prefs->get("reload_slide",false);
		$data['reload'] = $reload;
		if($reload) {
			$prefs->set("reload_slide",false);
			$prefs->save();
		}
		print(json_encode($data));
		exit();
	}
}
// NOTE: pour le moment, version module
// version slide viendra après, on déterminera le code en commun
function disp_slide($thisurl) {
	global $Nscreens,$url_miniature_stream,$prefs;
	?>
	<script type="text/javascript" src="cjs/module_slide_gestion.js"></script>
	<div id="slide_block">
	<h3>Prévisu</h3>
	<div class="slide_previsu screensize">
		<img class="slide_background" src="<?=$url_miniature_stream?>"/>
		<?php for($i=0; $i<max($Nscreens,8); $i++) {
			print('<img class="image image'.($i+1).'" src="cjs/img/vide.png" style="z-index:'.($i*10+10).';" />'."\n");
		}
		?>
		<div id="slide_scores" style="z-index:<?=intval($prefs->get("scoreboard_index",0))*10+5?>;">Les scores ne sont pas encore chargés</div>
	</div>
	<div class="btns">
		<button class="changer_tous" name="screen_changer_tous" value="" title="Valider les changements dans l'image">Valider les positions</button>
	</div>
	</div><?
}
