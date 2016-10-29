<?php
define('SOURCES_PARPAGE',12);
define('SOURCES_VISIBLEAVAP',2);
define('SOURCES_VISIBLEPAGES',2*SOURCES_VISIBLEAVAP+1);

function effacer_screen($screen) {
	global $prefs;
	$file = $prefs->screenFile($screen);
	if($file != "") {
		$prefs->screens[$screen] = array(
			'file' => "",
			'top' => 0,
			'left' => 0,
			'zoom' => 0,
		);
		$prefs->save();
		if(file_exists(SCREENS_DIR.$file)) {
			unlink(SCREENS_DIR.$file);
		}
	}
}

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
	if(isset($prefs->stars[$source])) {
		$prefs->stars[$source] = $prefs->stars[$source] ? false : true;
	} else {
		$prefs->stars[$source] = true;
	}
	$prefs->save();
	exit_redirect();
}

if(isset($_POST['sources_assign'])) {
	$screen = intval($_POST['sources_assign']);
	if($screen>0) {
		$source = $_POST['image_file'];
		$source = basename($source);
		$source = SOURCES_DIR.$source;
		if(file_exists($source)) {
			$ext = pathinfo($source,PATHINFO_EXTENSION);
			$screen_cible = sprintf(IMAGE_FORMAT,md5($source),$ext);
			effacer_screen($screen);
			copy($source,SCREENS_DIR.$screen_cible);
			$prefs->screens[$screen] = array(
				"file" => basename($screen_cible),
				"top" => 0,
				"left" => 0,
				"zoom" => 0,
			);
			if(isset($_POST['image_top']))
				$prefs->screens[$screen]['top'] = intval($_POST['image_top']);
			if(isset($_POST['image_left']))
				$prefs->screens[$screen]['left'] = intval($_POST['image_left']);
			if(isset($_POST['image_zoom']))
				$prefs->screens[$screen]['zoom'] = floatval($_POST['image_zoom']);
			$prefs->save();
		}
	}
	exit_redirect();
}

if(isset($_POST['changer_screen'])) {
	$screen = intval($_POST['changer_screen']);
	$file = $prefs->screenFile($screen);
	if($file != "") {
		if(isset($_POST['image_top']))
			$prefs->screens[$screen]['top'] = intval($_POST['image_top']);
		if(isset($_POST['image_left']))
			$prefs->screens[$screen]['left'] = intval($_POST['image_left']);
		if(isset($_POST['image_zoom']))
			$prefs->screens[$screen]['zoom'] = floatval($_POST['image_zoom']);
		$prefs->save();
	}
	exit_redirect();
}

if(isset($_POST["effacer_screen"])) {
	$screen = intval($_POST["effacer_screen"]);
	effacer_screen($screen);
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
	for($index=1; $index<=$Nscreens; $index++) {
		$imageurl = $prefs->screenFile($index);
		$imgPos = $prefs->screenPos($index);
		$isOn = $prefs->screenOn($index);
		$base_lien = dirname($thisurl);
		$lien = $base_lien ."/slide";
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
		<div class='screen screen<?=$index?>'>
			<form action="<?=$thisurl?>" name="screens" method="POST">
			<h3><a href="<?=$lien?>" target="_BLANK">Écran <?=$index?></a></h3>
			<?php
			if($isOn) {
				?><button class="btn_switch btn_switch_on" name="screen_switch" value="0" title="Activé, cliquer pour désactiver l'affichage des scores">ON</button><?
			} else {
				?><button class="btn_switch btn_switch_off" name="screen_switch" value="1" title="Désactivé, cliquer pour activer l'affichage des scores">OFF</button><?
			}
			?>
			<div class="pimage">
				<?php if($url_miniature_stream): ?>
				<img class="back_screen" src="<?=$url_miniature_stream?>" />
				<?php endif; ?>
				<img class="image" data-width="<?=$w?>" data-height="<?=$h?>" data-top="<?=$imgPos[1]?>" data-left="<?=$imgPos[0]?>" data-zoom="<?=$imgPos[2]?>" src="<?=$imageurl?>"/>
				<input type="hidden" name="screen_num" value="<?=$index?>"/>
				<input type="hidden" name="image_top" value="0"/>
				<input type="hidden" name="image_left" value="0"/>
				<input class="zoom" type="hidden" name="image_zoom" value="0"/>
				<button class="pos_btn topleft">@</button>
				<button class="pos_btn topright">@</button>
				<button class="pos_btn bottomleft">@</button>
				<button class="pos_btn bottomright">@</button>
				<button class="pos_btn centerleft">@</button>
				<button class="pos_btn centerright">@</button>
				<button class="pos_btn centertop">@</button>
				<button class="pos_btn centerbottom">@</button>
				<button class="pos_btn centercenter">@</button>
				<button class="pos_btn moveleft">&lt;</button>
				<button class="pos_btn moveright">&gt;</button>
				<button class="pos_btn movetop">^</button>
				<button class="pos_btn movebottom">v</button>
				<button class="pos_btn zoomin">+</button>
				<button class="pos_btn zoomout">-</button>
				<button class="pos_btn zoomzero">=</button>
			</div>
			<div class="btns">
				<button class="changer <?=$btns_classes?>" name="changer_screen" value="<?=$index?>">Changer</button>
				<button class="effacer <?=$btns_classes?>" name="effacer_screen" value="<?=$index?>">Effacer</button>
				<button class="twitter <?=$btns_classes2?>" name="twitter_screen" value="<?=$index?>" title="<?$twitter_title?>">Twitter le rébus</button>
			</div>
			</form>
		</div><?
	}
	?>
	</div>	<?
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
			?><a class="bouton_pagination" href="<?=thisurl(['sources_page'=>0])?>"><img class="pagination_star" src="cjs/nogrp.png"/></a><?
		} else {
			if($sources_page<$numSources) {
				$lesSources = array_slice($sources,$sources_page*SOURCES_PARPAGE,SOURCES_PARPAGE);
			}
			if($sources_page==0) $class="useless"; else $class = "";
			?><a class="bouton_pagination pagination_star" href="<?=thisurl(['sources_page'=>"stars"])?>"><img class="pagination_star" src="cjs/star.png"/></a><a class="bouton_pagination <?=$class?>" href="<?=thisurl(['sources_page'=>max(0,$sources_page-1)])?>">&lt;&mdash;</a><?
			$numPages = floor($numSources/SOURCES_PARPAGE);
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
		?><div class='source'>
			<form action="<?=$thisurl?>" name="sources" method="POST">
			<div class="pimage">
				<?php if($url_miniature_stream): ?>
				<img class="back_screen" src="<?=$url_miniature_stream?>" />
				<?php endif; ?>
				<img class="image" data-width="<?=$w?>" data-height="<?=$h?>" src="<?=$imageurl?>"/>
				<input type="hidden" name="image_file" value="<?=$name?>"/>
				<input type="hidden" name="image_top" value="0"/>
				<input type="hidden" name="image_left" value="0"/>
				<input class="zoom" type="hidden" name="image_zoom" value="0"/>
				<button class="pos_btn topleft">@</button>
				<button class="pos_btn topright">@</button>
				<button class="pos_btn bottomleft">@</button>
				<button class="pos_btn bottomright">@</button>
				<button class="pos_btn centerleft">@</button>
				<button class="pos_btn centerright">@</button>
				<button class="pos_btn centertop">@</button>
				<button class="pos_btn centerbottom">@</button>
				<button class="pos_btn centercenter">@</button>
				<button class="pos_btn moveleft">&lt;</button>
				<button class="pos_btn moveright">&gt;</button>
				<button class="pos_btn movetop">^</button>
				<button class="pos_btn movebottom">v</button>
				<button class="pos_btn zoomin">+</button>
				<button class="pos_btn zoomout">-</button>
				<button class="pos_btn zoomzero">=</button>
			</div>
			<div class="btns">
				<button class="effacer" name="sources_effacer" value="<?=$name?>">Effacer</button>
				<select class="assign" name="sources_assign">
					<option value="0">Afficher sur le stream</option>
					<?php
					for($screen=1; $screen<=$Nscreens; $screen++) {
						?><option value="<?=$screen?>">Écran <?=$screen?></option><?
					}
					?>
				</select>
			</div>
			<?php if(isset($prefs->stars[$name]) && $prefs->stars[$name]): ?>
			<button class="sources_star" name="sources_star" value="<?=$name?>"><img src="cjs/star.png"/></button>
			<?php else: ?>
			<button class="sources_star" name="sources_star" value="<?=$name?>"><img src="cjs/nogrp.png"/></button>
			<?php endif; ?>
			</form>
		</div><?
	}
	?>
	</div>
	<?
}
