<?php
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
		if(file_exists($file)) {
			unlink($file);
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

function disp_screens($thisurl) {
	global $Nscreens,$prefs,$url_miniature_stream;
	?>
	<div id="ecrans">
	<?php 
	for($index=1; $index<=$Nscreens; $index++) {
		$imageurl = $prefs->screenFile($index);
		$imgPos = $prefs->screenPos($index);
		$base_lien = dirname($thisurl);
		$lien = $base_lien ."/slide.php?screen=".$index;
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
			<h3><a href="<?=$lien?>" target="_BLANK">Écran <?=$index?></a><!-- <input type="text" class="lien" name="lien" value="<?=$lien?>" readonly/> --></h3>
			<div class="pimage">
				<?php if($url_miniature_stream): ?>
				<img class="back_screen" src="<?=$url_miniature_stream?>" />
				<?php endif; ?>
				<img class="image" data-width="<?=$w?>" data-height="<?=$h?>" data-top="<?=$imgPos[1]?>" data-left="<?=$imgPos[0]?>" data-zoom="<?=$imgPos[2]?>" src="<?=$imageurl?>"/>
				<input type="hidden" name="image_num" value="<?=$index?>"/>
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
	foreach(array_slice($sources,0,12) as $info) {
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
			</form>
		</div><?
	}
	?>
	</div>
	<?
}
