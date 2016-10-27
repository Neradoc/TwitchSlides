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

if(isset($_POST["effacer_source"])) {
	$file = $_POST["effacer_source"];
	$file = basename($file);
	$file = SOURCES_DIR.$file;
	if(file_exists($file)) {
		unlink($file);
	}
	exit_redirect();
}

if(isset($_POST['source_star'])) {
	$source = $_POST['source_star'];
	if(isset($prefs->stars[$source])) {
		$prefs->stars[$source] = $prefs->stars[$source] ? false : true;
	} else {
		$prefs->stars[$source] = true;
	}
	$prefs->save();
	exit_redirect();
}

if(isset($_POST['assign_source'])) {
	$screen = intval($_POST['assign_source']);
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
			$sizes = getimagesize(SCREENS_DIR.$imageurl);
			$imageurl = SCREENS_URL.$imageurl;
			$w = $sizes[0];
			$h = $sizes[1];
		} else {
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
				<button class="pos_btn zoomin">+</button>
				<button class="pos_btn zoomout">-</button>
				<button class="pos_btn zoomzero">=</button>
			</div>
			<div class="btns">
				<button class="changer" name="changer_screen" value="<?=$index?>">Changer</button>
				<button class="effacer" name="effacer_screen" value="<?=$index?>">Effacer</button>
				<button class="twitter" name="twitter_screen" value="<?=$index?>">Twitter le rébus</button>
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
	$source_page = 0;
	if($numSources > SOURCES_PARPAGE) {
		?><div class="pagination_sources"><?
		if(isset($_REQUEST['source_page'])) {
			$source_page = $_REQUEST['source_page'];
		} else {
			$source_page = 0;
		}
		if($source_page == "stars") {
			$lesSources = array_filter($sources,function($source) {
				global $prefs;
				$file = basename($source['file']);
				return isset($prefs->stars[$file]) && $prefs->stars[$file];
			});
			?><a class="bouton_pagination" href="<?=thisurl(['source_page'=>0])?>"><img class="pagination_star" src="cjs/nogrp.png"/></a><?
		} else {
			$source_page = intval($_REQUEST['source_page']);
			if($source_page<$numSources) {
				$lesSources = array_slice($sources,$source_page,SOURCES_PARPAGE);
			}
			if($source_page==0) $class="useless"; else $class = "";
			?><a class="bouton_pagination pagination_star" href="<?=thisurl(['source_page'=>"stars"])?>"><img class="pagination_star" src="cjs/star.png"/></a><a class="bouton_pagination <?=$class?>" href="<?=thisurl(['source_page'=>max(0,$source_page-SOURCES_PARPAGE)])?>">&lt;&mdash;</a><?
			$numPages = floor($numSources/SOURCES_PARPAGE);
			$curPage = floor($source_page/SOURCES_PARPAGE);
			$start = 0;
			$end = $numPages;
			if($numPages > SOURCES_VISIBLEPAGES) {
				$start = max(0,$curPage-SOURCES_VISIBLEAVAP);
				$start = min($numPages-SOURCES_VISIBLEPAGES,$start);
				$end = min($numPages,$curPage+SOURCES_VISIBLEAVAP);
				$end = max(SOURCES_VISIBLEPAGES,$end);
			}
			if($start == 1) $start = 0;
			if($end == $numPages-1) $end = $numPages;
			$sourcesList = range($start,$end);
			if($start>0) {
				?><a class="bouton_pagination useless" href="">...</a><?
			}
			foreach($sourcesList as $pageN) {
				if($curPage==$pageN) $class="useless"; else $class = "";
				$sourceN = $pageN * SOURCES_PARPAGE;
				?><a class="bouton_pagination <?=$class?>" href="<?=thisurl(['source_page'=>$sourceN])?>"><?=$pageN+1 ?></a><?
			}
			if($end<$numPages) {
				?><a class="bouton_pagination useless" href="">...</a><?
			}
			if($curPage>=$pageN) $class="useless"; else $class = "";
			?><a class="bouton_pagination <?=$class?>" href="<?=thisurl(['source_page'=>min($sourceN,$source_page+SOURCES_PARPAGE)]) ?>">&mdash;&gt;</a><?
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
				<button class="pos_btn zoomin">+</button>
				<button class="pos_btn zoomout">-</button>
				<button class="pos_btn zoomzero">=</button>
			</div>
			<div class="btns">
				<button class="effacer" name="effacer_source" value="<?=$name?>">Effacer</button>
				<select class="assign" name="assign_source">
					<option value="0">Afficher sur le stream</option>
					<?php
					for($screen=1; $screen<=$Nscreens; $screen++) {
						?><option value="<?=$screen?>">Écran <?=$screen?></option><?
					}
					?>
				</select>
			</div>
			<?php if(isset($prefs->stars[$name]) && $prefs->stars[$name]): ?>
			<button class="source_star" name="source_star" value="<?=$name?>"><img src="cjs/star.png"/></button>
			<?php else: ?>
			<button class="source_star" name="source_star" value="<?=$name?>"><img src="cjs/nogrp.png"/></button>
			<?php endif; ?>
			</form>
		</div><?
	}
	?>
	</div>
	<?
}
