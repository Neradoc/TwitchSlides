<?php
define('SOURCES_PARPAGE',12);
define('SOURCES_VISIBLEAVAP',2);
define('SOURCES_VISIBLEPAGES',2*SOURCES_VISIBLEAVAP+1);

if(isset($_POST["sources_effacer"])) {
	$file = $_POST["sources_effacer"];
	$file = basename($file);
	$file = SOURCES_DIR.$file;
	if(file_exists($file)) {
		unlink($file);
	}
	exit_redirect();
}

if(isset($_POST['sources_star'])) {
	$source = $_POST['sources_star'];
	if(isset($prefs->stars[$source]) && $prefs->stars[$source]) {
		unset($prefs->stars[$source]);
	} else {
		$prefs->stars[$source] = true;
	}
	$prefs->save();
	exit_redirect();
}

if(isset($_POST['sources_assign1']) || isset($_POST['sources_assign2'])) {
	if($_POST['sources_assign1'] != "") {
		$screenIns = $_POST['sources_assign1'];
	} else {
		$screenIns = $_POST['sources_assign2'];
	}
	$source = $_POST['image_file'];
	$source = basename($source);
	$source = SOURCES_DIR.$source;
	if(file_exists($source)) {
		$ext = pathinfo($source,PATHINFO_EXTENSION);
		$screen_cible = sprintf(IMAGE_FORMAT,md5_file($source),$ext);
		copy($source,SCREENS_DIR.$screen_cible);
		$file = basename($screen_cible);
		$top = 0;
		$left = 0;
		$zoom = 0;
		if(isset($_POST['image_top']))
			$top = intval($_POST['image_top']);
		if(isset($_POST['image_left']))
			$left = intval($_POST['image_left']);
		if(isset($_POST['image_zoom']))
			$zoom = floatval($_POST['image_zoom']);
		#
		if($screenIns[0] == "+") {
			$screenIns = intval(substr($screenIns,1));
			$prefs->insertScreen($screenIns,$file,$top,$left,$zoom);
		} elseif($screenIns[0] == "=") {
			$screenIns = intval(substr($screenIns,1));
			$prefs->setScreen($screenIns,$file,$top,$left,$zoom,-1);
		} else {
			$prefs->addScreen($file,$top,$left,$zoom);
		}
		$prefs->save();
	}
	exit_redirect();
}

if(isset($_POST['screen_moveto'])) {
	$screen = intval($_POST["screen_num"]);
	$autre = intval($_POST['screen_moveto']);
	$prefs->switch_screens($screen,$autre);
	$prefs->save();
	exit_redirect();
}

if(isset($_POST['screen_changer'])) {
	$screen = intval($_POST['screen_num']);
	$file = $prefs->screenFile($screen);
	$stamp = $prefs->screenTime($screen);
	if($file != "") {
		$top = null;
		$left = null;
		$zoom = null;
		$stamp = null;
		if(isset($_POST['image_top']))
			$top = intval($_POST['image_top']);
		if(isset($_POST['image_left']))
			$left = intval($_POST['image_left']);
		if(isset($_POST['image_zoom']))
			$zoom = floatval($_POST['image_zoom']);
		if(isset($_POST['screen_timer'])) {
			$stamp = intval($_POST['screen_timer'])*60 + time();
		} else {
			$stamp = -1;
		}
		$prefs->setScreen($screen,$file,$top,$left,$zoom,$stamp);
		$prefs->save();
	}
	exit_redirect();
}

if(isset($_POST['screen_timer'])) {
	$screen = intval($_POST['screen_num']);
	$file = $prefs->screenFile($screen);
	$stamp = $prefs->screenTime($screen);
	$timer = intval($_POST['screen_timer']);
	if($file != "") {
		$stamp = time() - $timer*60;
		$prefs->setScreen($screen,$file,null,null,null,$stamp);
		$prefs->save();
	}
	exit_redirect();
}

if(isset($_POST["screen_effacer"])) {
	$screen = intval($_POST["screen_effacer"]);
	$prefs->effacer_screen($screen);
	exit_redirect();
}

if(isset($_POST['screen_switch'])) {
	$screen = intval($_POST["screen_num"]);
	$value = intval($_POST['screen_switch']);
	if(isset($prefs->screens[$screen])) {
		$prefs->screens[$screen]['on'] = $value?true:false;
		$prefs->save();
	}
	exit_redirect();
}

function disp_screens($thisurl) {
	global $Nscreens,$prefs,$url_miniature_stream;
	?>
	<div id="screens">
	<?php
	for($index=0; $index<$Nscreens; $index++) {
		$imageurl = $prefs->screenFile($index);
		$imgPos = $prefs->screenPos($index);
		$isOn = $prefs->screenOn($index);
		$timestamp = $prefs->screenTime($index);
		$base_lien = dirname($thisurl);
		$lien = $base_lien ."/slide";
		$twitter_title = "Poster l'image sur twitter";
		if($imageurl != "" && file_exists(SCREENS_DIR.$imageurl)) {
			$btns_classes = "";
			$btns_classes2 = "";
			if(in_array($imageurl,$prefs->tweets)) {
				$btns_classes2 = "disabled";
				$twitter_title = "Image déjà twittée";
			}
			$sizes = getimagesize(SCREENS_DIR.$imageurl);
			$imageurl = SCREENS_URL.$imageurl;
			$w = $sizes[0];
			$h = $sizes[1];
		} else {
			$btns_classes = "disabled";
			$btns_classes2 = $btns_classes;
			$w = 0;
			$h = 0;
		}
		?>
		<div class='screen screen<?=$index?> module_screen_block'>
			<form action="<?=$thisurl?>" name="screens" method="POST">
			<div class="headbtns">
				<? if($index>0): ?>
				<button class="moveprev_head" name="screen_moveto" value="<?= $index-1?>"><img class="offer" src="cjs/img/fleche_left.png"/><img class="hover" src="cjs/img/fleche_left_hover.png"/></button>
				<? endif; ?>
				<? if($index<$Nscreens-1): ?>
				<button class="movenext_head" name="screen_moveto" value="<?= $index+1?>"><img class="offer" src="cjs/img/fleche_right.png"/><img class="hover" src="cjs/img/fleche_right_hover.png"/></button>
				<? endif; ?>
			</div>
			<h3><a href="<?=$lien?>" target="_BLANK">Image <?=$index+1?></a></h3>
			<?php
			if($isOn) {
				?><button class="btn_switch btn_switch_on" name="screen_switch" value="0" title="Activé, cliquer pour désactiver l'affichage des scores">ON</button><?
			} else {
				?><button class="btn_switch btn_switch_off" name="screen_switch" value="1" title="Désactivé, cliquer pour activer l'affichage des scores">OFF</button><?
			}
			?>
			<div class="pimage screensize">
				<?php if($url_miniature_stream): ?>
				<img class="back_screen" src="<?=$url_miniature_stream?>" />
				<?php endif; ?>
				<img class="image" data-width="<?=$w?>" data-height="<?=$h?>" data-top="<?=$imgPos[1]?>" data-left="<?=$imgPos[0]?>" data-zoom="<?=$imgPos[2]?>" src="<?=$imageurl?>"/>
				<input type="hidden" class="timestamp" name="" value="<?=$timestamp?>"/>
				<input type="hidden" name="screen_num" value="<?=$index?>"/>
				<input type="hidden" name="image_top" value="0"/>
				<input type="hidden" name="image_left" value="0"/>
				<input class="zoom" type="hidden" name="image_zoom" value="0"/>
				<button class="pos_btn topleft"><img src="cjs/img/crosshair.png"/></button>
				<button class="pos_btn topright"><img src="cjs/img/crosshair.png"/></button>
				<button class="pos_btn bottomleft"><img src="cjs/img/crosshair.png"/></button>
				<button class="pos_btn bottomright"><img src="cjs/img/crosshair.png"/></button>
				<button class="pos_btn centerleft"><img src="cjs/img/crosshair.png"/></button>
				<button class="pos_btn centerright"><img src="cjs/img/crosshair.png"/></button>
				<button class="pos_btn centertop"><img src="cjs/img/crosshair.png"/></button>
				<button class="pos_btn centerbottom"><img src="cjs/img/crosshair.png"/></button>
				<button class="pos_btn centercenter"><img src="cjs/img/crosshair.png"/></button>
				<button class="pos_btn moveleft">︎<img src="cjs/img/fleche_left.png"/></button>
				<button class="pos_btn moveright"><img src="cjs/img/fleche_right.png"/></button>
				<button class="pos_btn movetop"><img src="cjs/img/fleche_top.png"/></button>
				<button class="pos_btn movebottom"><img src="cjs/img/fleche_bottom.png"/></button>
				<button class="pos_btn zoomin">+</button>
				<button class="pos_btn zoomout">-</button>
				<button class="pos_btn zoomzero">=</button>
				<? if($index>0): ?>
				<button class="pos_btn moveprev" name="screen_moveto" value="<?= $index-1?>">➤<?= $index ?></button>
				<? endif; ?>
				<? if($index<$Nscreens-1): ?>
				<button class="pos_btn movenext" name="screen_moveto" value="<?= $index+1?>">➤<?= $index+2 ?></button>
				<? endif; ?>
			</div>
			<div class="btns">
				<button class="changer <?=$btns_classes?>" name="screen_changer" value="<?=$index?>" title="Valider les changements dans l'image">Valider</button>
				<button class="effacer" name="screen_effacer" value="<?=$index?>" title="Enlever l'image de l'écran">Effacer</button>
				<button class="twitter <?=$btns_classes2?>" name="twitter_screen" value="<?=$index?>" title="<?$twitter_title?>">Twitter l'image</button>
			</div>
			</form>
			<form action="<?=$thisurl?>" name="screen_timer" method="POST">
			<input type="hidden" name="screen_num" value="<?=$index?>"/>
			<div class="screen_timer" title="Minutes depuis que l'image a été mise sur l'écran"><input type="texte" name="screen_timer" value="" class="screen_timer_text"/>
			<?php
			if(isset($GLOBALS['calc_score']) && trim($GLOBALS['calc_score']) != "") {
				print('<img src="cjs/img/icone-scoring.png"/>');
			} else {
				print('<img src="cjs/img/icone-horloge.png"/>');
			}
			?>
			</div>
			</form>
		</div><?
	}
	?>
	</div>
	<?
}

function disp_sources($thisurl) {
	global $Nscreens,$prefs,$url_miniature_stream;
	?>
	<div id="sources">
	<?php
	$sources = array();
	foreach(glob(SOURCES_GLOB) as $source) {
		if(file_exists($source)) {
			$sources[] = array(
				'file' => $source,
				'date' => filemtime($source),
			);
		}
	}
	usort($sources,function($a,$b) {
		return $b['date'] - $a['date'];
	});
	$lesSources = $sources;
	$numSources = count($sources);
	if($numSources > SOURCES_PARPAGE) {
		?><div class="pagination_sources"><?
		$sources_page = 0;
		if(isset($_REQUEST['sources_page'])) {
			$sources_page = $_REQUEST['sources_page'];
		}
		if($sources_page === "stars") {
			$lesSources = array_filter($sources,function($source) {
				global $prefs;
				$file = basename($source['file']);
				return isset($prefs->stars[$file]) && $prefs->stars[$file];
			});
			?><a class="bouton_pagination" href="<?=thisurl(['sources_page'=>0])?>" title="Toutes les images"><img class="pagination_star" src="cjs/img/nogrp.png"/></a><?
		} else {
			$numPages = floor(($numSources-1)/SOURCES_PARPAGE);
			$sources_page = max(0,min($numPages,$sources_page));
			$lesSources = array_slice($sources,$sources_page*SOURCES_PARPAGE,SOURCES_PARPAGE);
			if($sources_page==0) $class="useless"; else $class = "";
			?><a class="bouton_pagination pagination_star" href="<?=thisurl(['sources_page'=>"stars"])?>" title="Images favorites"><img class="pagination_star" src="cjs/img/star.png"/></a><a class="bouton_pagination <?=$class?>" href="<?=thisurl(['sources_page'=>max(0,$sources_page-1)])?>">&lt;&mdash;</a><?
			$start = 0;
			$end = $numPages;
			if($numPages > SOURCES_VISIBLEPAGES) {
				$start = max(0,$sources_page-SOURCES_VISIBLEAVAP);
				$start = min($numPages-SOURCES_VISIBLEPAGES,$start);
				$end = min($numPages,$sources_page+SOURCES_VISIBLEAVAP);
				$end = max(SOURCES_VISIBLEPAGES,$end);
			}
			if($start == 1) $start = 0;
			if($end == $numPages-1) $end = $numPages;
			$sourcesList = range($start,$end);
			if($start>0) {
				?><a class="bouton_pagination" href="<?=thisurl(['sources_page'=>0]) ?>">...</a><?
			}
			foreach($sourcesList as $pageN) {
				if($sources_page==$pageN) $class="current"; else $class = "";
				?><a class="bouton_pagination <?=$class?>" href="<?=thisurl(['sources_page'=>$pageN])?>"><?=$pageN+1 ?></a><?
			}
			if($end<$numPages) {
				?><a class="bouton_pagination" href="<?=thisurl(['sources_page'=>$numPages]) ?>">...</a><?
			}
			if($sources_page>=$pageN) $class="useless"; else $class = "";
			?><a class="bouton_pagination <?=$class?>" href="<?=thisurl(['sources_page'=>min($numPages,$sources_page+1)]) ?>">&mdash;&gt;</a><?
		}
		?></div><?
	}
	foreach($lesSources as $info) {
		$imageurl = $info['file'];
		$iu = $imageurl;
		$name = basename($imageurl);
		if($imageurl != "" && file_exists($imageurl)) {
			$sizes = getimagesize($imageurl);
			$w = $sizes[0];
			$h = $sizes[1];
		} else {
			continue;
		}
		?><div class='source module_screen_block'>
			<form action="<?=$thisurl?>" name="sources" method="POST">
			<div class="pimage screensize">
				<?php if($url_miniature_stream): ?>
				<img class="back_screen" src="<?=$url_miniature_stream?>" />
				<?php endif; ?>
				<img class="image" data-width="<?=$w?>" data-height="<?=$h?>" src="<?=$imageurl?>"/>
				<input type="hidden" name="image_file" value="<?=$name?>"/>
				<input type="hidden" name="image_top" value="0"/>
				<input type="hidden" name="image_left" value="0"/>
				<input class="zoom" type="hidden" name="image_zoom" value="0"/>
				<button class="pos_btn topleft"><img src="cjs/img/crosshair.png"/></button>
				<button class="pos_btn topright"><img src="cjs/img/crosshair.png"/></button>
				<button class="pos_btn bottomleft"><img src="cjs/img/crosshair.png"/></button>
				<button class="pos_btn bottomright"><img src="cjs/img/crosshair.png"/></button>
				<button class="pos_btn centerleft"><img src="cjs/img/crosshair.png"/></button>
				<button class="pos_btn centerright"><img src="cjs/img/crosshair.png"/></button>
				<button class="pos_btn centertop"><img src="cjs/img/crosshair.png"/></button>
				<button class="pos_btn centerbottom"><img src="cjs/img/crosshair.png"/></button>
				<button class="pos_btn centercenter"><img src="cjs/img/crosshair.png"/></button>
				<button class="pos_btn moveleft">︎<img src="cjs/img/fleche_left.png"/></button>
				<button class="pos_btn moveright"><img src="cjs/img/fleche_right.png"/></button>
				<button class="pos_btn movetop"><img src="cjs/img/fleche_top.png"/></button>
				<button class="pos_btn movebottom"><img src="cjs/img/fleche_bottom.png"/></button>
				<button class="pos_btn zoomin">+</button>
				<button class="pos_btn zoomout">-</button>
				<button class="pos_btn zoomzero">=</button>
			</div>
			<div class="btns">
				<button class="effacer" name="sources_effacer" value="<?=$name?>" title="Retirer l'image du serveur (irréversible)">Effacer</button>
				<!-- <button class="" name="sources_assign" value="">Ajouter</button> -->
				<select class="assign" name="sources_assign1" title="Ajouter l'image aux images affichées">
					<option value="">Ajouter</option>
					<option value="0">Premier plan</option>
					<?php
					for($screen=$Nscreens-1; $screen>0; $screen--) {
						?><option value="+<?=$screen?>">Devant Image <?=$screen?></option><?
					}
					?>
					<option value="+0">Arrière plan</option>
				</select>
				<select class="assign" name="sources_assign2" title="Remplacer une image (et conserver le timer)">
					<option value="">Remplacer</option>
					<?php
					for($screen=0; $screen<$Nscreens; $screen++) {
						?><option value="=<?=$screen?>">Image <?=$screen+1?></option><?
					}
					?>
				</select>
			</div>
			<?php if(isset($prefs->stars[$name]) && $prefs->stars[$name]): ?>
			<button class="sources_star" name="sources_star" value="<?=$name?>" title="Retirer cette image des favorites"><img src="cjs/img/star.png"/></button>
			<?php else: ?>
			<button class="sources_star" name="sources_star" value="<?=$name?>" title="Marquer cette image comme favorite"><img src="cjs/img/nogrp.png"/></button>
			<?php endif; ?>
			</form>
		</div><?
	}
	?>
	</div>
	<?
}
