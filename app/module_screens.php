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
	// sauver pour mettre à jour *stars*
	$prefs->save();
	exit_redirect();
}

if(isset($_POST['sources_star']) && isset($_POST['image_file'])) {
	$source = $_POST['image_file'];
	$cat = $_POST['sources_star'];
	if($cat == "") {
		unset($prefs->stars[$source]);
	} else {
		$prefs->stars[$source] = $cat;
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
			$prefs->setScreen($screenIns,$file,$top,$left,$zoom,null);
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

if(isset($_POST['screen_timer_activate'])) {
	$screen = intval($_POST['screen_num']);
	$prefs->active_screen($screen);
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
			$stamp = null;
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

function boutons_on_image() {
?>
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
				<button class="pos_btn moveleft"><img src="cjs/img/fleche_left.png"/></button>
				<button class="pos_btn moveright"><img src="cjs/img/fleche_right.png"/></button>
				<button class="pos_btn movetop"><img src="cjs/img/fleche_top.png"/></button>
				<button class="pos_btn movebottom"><img src="cjs/img/fleche_bottom.png"/></button>
				<button class="pos_btn zoomin">+</button>
				<button class="pos_btn zoomout">-</button>
				<button class="pos_btn zoomzero">=</button>
<?php
}

function disp_screens($thisurl) {
	global $Nscreens,$prefs,$url_miniature_stream;
	?>
	<div id="screens">
	<?php
	$active = $prefs->active_screen();
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
		if($active === $index) {
			$title_timer_btn = "Désactiver le mode jeu";
		} else {
			$title_timer_btn = "Activer le mode jeu";
		}
		if(isset($GLOBALS['calc_score']) && trim($GLOBALS['calc_score']) != "") {
			$html_image_timer_btn = '<img src="cjs/img/icone-scoring.png"/>';
		} else {
			$html_image_timer_btn = '<img src="cjs/img/icone-horloge.png"/>';
		}
		?>
		<div class='screen module_box screen<?=$index?> module_screen_block <?=$active===$index?"active":""?>'>
			<form action="<?=$thisurl?>" name="screens" method="POST">
			<div class="headbtns">
				<? if($index>0): ?>
				<button class="moveprev_head" name="screen_moveto" value="<?= $index-1?>"><img class="offer" src="cjs/img/fleche_left.png"/><img class="hover" src="cjs/img/fleche_left_hover.png"/></button>
				<? endif; ?>
				<? if($index<$Nscreens-1): ?>
				<button class="movenext_head" name="screen_moveto" value="<?= $index+1?>"><img class="offer" src="cjs/img/fleche_right.png"/><img class="hover" src="cjs/img/fleche_right_hover.png"/></button>
				<? endif; ?>
			</div>
			<h3><a href="<?=$lien?>" target="_BLANK">Image <?=$index+1 ?></a></h3>
			<button class="effacer_croix" name="screen_effacer" value="<?=$index?>" title="Enlever l'image de l'écran"><img src="cjs/img/bouton_croix_bis.png"/></button>
			<button class="temp_pop effacer_croix_valide" name="screen_effacer" value="<?=$index?>">Enlever l'image de l'écran</button>
			<?php
			if($isOn) {
				?><button class="btn_switch btn_switch_on" name="screen_switch" value="0" title="Cliquer pour masquer l'image">ON</button><?
			} else {
				?><button class="btn_switch btn_switch_off" name="screen_switch" value="1" title="Cliquer pour afficher l'image">OFF</button><?
			}
			?>
			<div class="pimage screensize">
				<?php if($url_miniature_stream): ?>
				<img class="back_screen" src="<?=$url_miniature_stream?>" />
				<?php endif; ?>
				<img draggable="false" class="image" data-width="<?=$w?>" data-height="<?=$h?>" data-top="<?=$imgPos[1]?>" data-left="<?=$imgPos[0]?>" data-zoom="<?=$imgPos[2]?>" src="<?=$imageurl?>"/>
				<input type="hidden" class="timestamp" name="" value="<?=$timestamp?>"/>
				<input type="hidden" name="screen_num" value="<?=$index?>"/>
				<?php boutons_on_image(); ?>
			</div>
			<div class="btns">
				<button class="twitter <?=$btns_classes2?>" name="twitter_screen" value="<?=$index?>" title="<?=$twitter_title?>"><img class="img_twitter_off" src="cjs/img/twitter-off.png" /><img class="img_twitter" src="cjs/img/twitter.png" /> Twitter</button>
				<button class="changer <?=$btns_classes?>" name="screen_changer" value="<?=$index?>" title="Valider les changements dans l'image">Valider position</button>
				<!-- <button class="effacer" name="screen_effacer" value="<?=$index?>" title="Enlever l'image de l'écran">Effacer</button> -->
			</div>
			</form>
			<form action="<?=$thisurl?>" name="screen_timer" method="POST">
			<input type="hidden" name="screen_num" value="<?=$index?>"/>
			<div class="screen_timer"><label class="screen_timer_label">Jeu: <input type="texte" name="screen_timer" value="" class="screen_timer_text" title="Minutes depuis que l'image a été mise sur l'écran"/></label>
			<input type="submit" style="display:none;" name="dummy" value=""/>
			<button class="screen_timer_btn btn_image" name="screen_timer_activate" value="<?=$index?>" title="<?=$title_timer_btn?>"><?=$html_image_timer_btn?></button>
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
	$categories = $prefs->categories();
	$categoriesSize = $prefs->categoriesSize();
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
		if(isset($categoriesSize[$sources_page])) {
			$lesSources = array_filter($sources,function($source) use($sources_page) {
				global $prefs;
				$file = basename($source['file']);
				if(!isset($prefs->stars[$file])) return false;
				if($prefs->stars[$file] === $sources_page) return true;
				if($prefs->stars[$file] === true && $sources_page === "star") return true;
				return false;
			});
			foreach($categoriesSize as $categorie => $csize) {
				$image = $categories[$categorie];
				if($categorie === $sources_page) $class = "current";
				else $class = "";
				?><a class="bouton_pagination <?=$class?>" href="<?=thisurl(['sources_page'=>$categorie])?>" title="<?=$categorie?>"><img class="pagination_star" src="<?=$image?>"/><span class="categorie_size"><?=$csize?></span></a><?
			}		
			?><a class="bouton_pagination" href="<?=thisurl(['sources_page'=>0])?>" title="Toutes les images"><img class="pagination_star" src="cjs/img/nogrp.png"/><span class="categorie_size"><?=$numSources?></span></a><?
		} else {
			$numPages = floor(($numSources-1)/SOURCES_PARPAGE);
			$sources_page = max(0,min($numPages,$sources_page));
			$lesSources = array_slice($sources,$sources_page*SOURCES_PARPAGE,SOURCES_PARPAGE);
			foreach($categoriesSize as $categorie => $csize) {
				$image = $categories[$categorie];
				?><a class="bouton_pagination" href="<?=thisurl(['sources_page'=>$categorie])?>" title="<?=$categorie?>"><img class="pagination_star" src="<?=$image?>"/><span class="categorie_size"><?=$csize?></span></a><?
			}		
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
				?><a class="bouton_pagination" href="<?=thisurl(['sources_page'=>0]) ?>">1</a><?
				?><a class="bouton_pagination" href="<?=thisurl(['sources_page'=>max(1,$sources_page-5)])?>">...</a><?
			}
			foreach($sourcesList as $pageN) {
				if($sources_page==$pageN) $class="current"; else $class = "";
				?><a class="bouton_pagination <?=$class?>" href="<?=thisurl(['sources_page'=>$pageN])?>"><?=$pageN+1 ?></a><?
			}
			if($end<$numPages) {
				?><a class="bouton_pagination" href="<?=thisurl(['sources_page'=>min($numPages-1,$sources_page+5)]) ?>">...</a><?
				?><a class="bouton_pagination" href="<?=thisurl(['sources_page'=>$numPages]) ?>"><?=$numPages+1?></a><?
			}
		}
		?></div><?
	}
	foreach($lesSources as $info) {
		$imageurl = $info['file'];
		$name = basename($imageurl);
		if($imageurl != "" && file_exists($imageurl)) {
			$sizes = getimagesize($imageurl);
			$w = $sizes[0];
			$h = $sizes[1];
		} else {
			continue;
		}
		?><div class='source module_box module_screen_block'>
			<form action="<?=$thisurl?>" name="sources" method="POST">
			<div class="pimage screensize">
				<?php if($url_miniature_stream): ?>
				<img class="back_screen" src="<?=$url_miniature_stream?>" />
				<?php endif; ?>
				<img  draggable="false" class="image" data-width="<?=$w?>" data-height="<?=$h?>" src="<?=$imageurl?>"/>
				<input type="hidden" name="image_file" value="<?=$name?>"/>
				<?php boutons_on_image(); ?>
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
			<?php
			$cat_file = "cjs/cats/nogrp.png";
			$cat_title = "Assigner une cat&eacute;gorie à cette image";
			if(isset($prefs->stars[$name])) {
				if(isset($categories[$prefs->stars[$name]])) {
					$cat_file = $categories[$prefs->stars[$name]];
				} else {
					$cat_file = "cjs/cats/star.png";
				}
			}
			?>
			<span class="sources_star" title="<?=$cat_title?>"><img src="<?=$cat_file?>"/></span>
			<div class="sources_star_pannel">
			<?php foreach($categories as $ctag => $cimg): ?>
				<button class="cat_btn" name="sources_star" value="<?=$ctag?>" title="<?=$ctag?>"><img src="<?=$cimg?>"/></button>
			<?php endforeach; ?>
			<button class="cat_btn" name="sources_star" value="" title="Retirer cette image de toute cat&eacute;gorie"><img src="cjs/cats/nogrp.png"/></button>
			</div>
			</form>
		</div><?
	}
	?>
	</div>
	<?
}
