<?php
define("DEBUG",false);

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include_once("head.php");
include_once("prefs.php");
include_once("twitter.php");

$prefs = new PrefsManager();

function is_image($file) {
	if(!file_exists($file)) { return false; }
	$mime = mime_content_type($file);
	if(preg_match('`image/(jpeg|png)`i',$mime)) return true;
	return false;
}

function effacer_screen($screen) {
	global $prefs;
	$file = $prefs->screenFile($screen);
	if($file != "") {
		$prefs->screens[$screen] = array(
			'file' => "",
			'top' => 0,
			'left' => 0,
		);
		$prefs->save();
		if(file_exists($file)) {
			unlink($file);
		}
	}
}

include_once("module_strawpoll.php");
include_once("module_upload.php");
include_once("module_scoreboard.php");

if(isset($_POST["effacer_screen"])) {
	$screen = intval($_POST["effacer_screen"]);
	effacer_screen($screen);
	exit_redirect();
}

if(isset($_POST["twitter_screen"])) {
	$screen = intval($_POST["twitter_screen"]);
	$file = $prefs->screenFile($screen);
	if($file && file_exists($file)) {
		$urlImage = dirname($thisurl).$file;
		if(in_array($urlImage,$prefs->tweets)) {
			twitterImage($urlImage);
			$prefs->tweets[] = $urlImage;
			$prefs->save();
		}
		break;
	}
	exit_redirect();
}

if(isset($_POST["effacer_source"])) {
	$file = $_POST["effacer_source"];
	$file = basename($file);
	$file = "sources/".$file;
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
		$source = "sources/".$source;
		if(file_exists($source)) {
			$ext = pathinfo($source,PATHINFO_EXTENSION);
			$screen_cible = sprintf($image_format,md5($source),$ext);
			effacer_screen($screen);
			copy($source,$screen_cible);
			$prefs->screens[$screen] = array(
				"file" => basename($screen_cible),
				"top" => 0,
				"left" => 0,
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
}

if(!empty($_POST) || !empty($_FILES)) {
	exit_redirect();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
	<meta charset="utf-8" />
	<link rel="shortcut icon" href="favicon.png" />
	<meta name="viewport" content="width=412">
	<title>Les écrans de realmyop</title>
	<link rel='stylesheet' href='gestion.css' type='text/css' />
	<style type="text/css" title="text/css"></style>
	<script type="text/javascript" src="jquery2.js"></script>
	<script type="text/javascript" language="javascript" charset="utf-8">
	// dimensions du cadre simulant l'écran
	//var fw = 400;
	//var fh = 225;
	var fw = 1920;
	var fh = 1080;
	var movingImage = false;
	var movingStart = [0,0];
	$(function() {
		$(".assign").change(function() {
			var source = $(this).closest(".source");
			var parent = source.find(".pimage");
			var image = parent.find(".image");
			var top = image.offset().top-parent.offset().top;
			var left = image.offset().left-parent.offset().left;
			source.find('input[name="image_top"]').val(Math.floor(4.8*top));
			source.find('input[name="image_left"]').val(Math.floor(4.8*left));
			//source.find('input[name="image_zoom"]').val(0);
			$(this).closest("form").submit();
		});
		$(".changer").click(function() {
			var screen = $(this).closest(".screen");
			var parent = screen.find(".pimage");
			var image = parent.find(".image");
			var top = image.offset().top-parent.offset().top;
			var left = image.offset().left-parent.offset().left;
			screen.find('input[name="image_top"]').val(Math.floor(4.8*top));
			screen.find('input[name="image_left"]').val(Math.floor(4.8*left));
			//screen.find('input[name="image_zoom"]').val(0);
			console.log(Math.floor(4.8*top));
			console.log(Math.floor(4.8*left));
		});
		$(".lien").click(function() {
			$(this).select();
		});
		$(".screen,.source").each(function() {
			var that = this;
			var img = $(this).find(".pimage .image");
			img.on("load",function() {
				var itop = img.data("top");
				var ileft = img.data("left");
				if(!itop) itop = 0;
				if(!ileft) ileft = 0;
				var iw = img.data("width");
				var ih = img.data("height");
				var zoom = img.data("zoom");
				console.log(zoom);
				var h,w;
				if(iw>fw || ih>fh) {
					if(iw/ih>16/9) {
						w = fw;
						h = ih * fw/iw;
					} else {
						w = iw * fh/ih;
						h = fh;
					}
				} else {
					w = iw;
					h = ih;
				}
				if(zoom>0) {
					w = Math.floor(w*zoom);
					h = Math.floor(h*zoom);
					$(that).find('input[name="image_zoom"]').val(zoom);
				}
				img.css({
					position:"absolute",
					left: (ileft?ileft:0)+"px",
					top: (itop?itop:0)+"px",
					width: w+"px",
					height: h+"px",
				});
			});
		});
		//
		$(".pos_btn.topleft").click(function() {
			$(this).siblings(".image").css({
				top:"0px", bottom:"auto",
				left:"0px", right:"auto",
			});
			$(this).closest(".screen").addClass("modified");
			return false;
		});
		$(".pos_btn.topright").click(function() {
			$(this).siblings(".image").css({
				top:"0px", bottom:"auto",
				left:"auto", right:"0px",
			});
			$(this).closest(".screen").addClass("modified");
			return false;
		});
		$(".pos_btn.bottomleft").click(function() {
			$(this).siblings(".image").css({
				top:"auto", bottom:"0px",
				left:"0px", right:"auto",
			});
			$(this).closest(".screen").addClass("modified");
			return false;
		});
		$(".pos_btn.bottomright").click(function() {
			$(this).siblings(".image").css({
				top:"auto", bottom:"0px",
				left:"auto", right:"0px",
			});
			$(this).closest(".screen").addClass("modified");
			return false;
		});
		//
		$(".pos_btn.centerleft").click(function() {
			img = $(this).siblings(".image");
			var h=img.height(),w=img.width();
			img.css({
				top:Math.floor(fh/2-h/2)+"px", bottom:"auto",
				left:"0px", right:"auto",
			});
			$(this).closest(".screen").addClass("modified");
			return false;
		});
		$(".pos_btn.centerright").click(function() {
			img = $(this).siblings(".image");
			var h=img.height(),w=img.width();
			img.css({
				top:Math.floor(fh/2-h/2)+"px", bottom:"auto",
				left:"auto", right:"0px",
			});
			$(this).closest(".screen").addClass("modified");
			return false;
		});
		$(".pos_btn.centertop").click(function() {
			img = $(this).siblings(".image");
			var h=img.height(),w=img.width();
			img.css({
				top:"0px", bottom:"auto",
				left:Math.floor(fw/2-w/2)+"px", right:"auto",
			});
			$(this).closest(".screen").addClass("modified");
			return false;
		});
		$(".pos_btn.centerbottom").click(function() {
			img = $(this).siblings(".image");
			var h=img.height(),w=img.width();
			img.css({
				top:"auto", bottom:"0px",
				left:Math.floor(fw/2-w/2)+"px", right:"auto",
			});
			$(this).closest(".screen").addClass("modified");
			return false;
		});
		$(".pos_btn.centercenter").click(function() {
			img = $(this).siblings(".image");
			var h=img.height(),w=img.width();
			img.css({
				top:Math.floor(fh/2-h/2)+"px", bottom:"auto",
				left:Math.floor(fw/2-w/2)+"px", right:"auto",
			});
			$(this).closest(".screen").addClass("modified");
			return false;
		});
		$(".pos_btn.zoomin").click(function() {
			img = $(this).siblings(".image");
			zoom = $(this).siblings(".zoom");
			if(zoom.val() == 0) zoom.val(img.data("width")/img.width());
			zoom.val(zoom.val()*1.1)
			img.height(img.data("height")*zoom.val());
			img.width(img.data("width")*zoom.val());
			$(this).closest(".screen").addClass("modified");
			return false;
		});
		$(".pos_btn.zoomout").click(function() {
			img = $(this).siblings(".image");
			zoom = $(this).siblings(".zoom");
			if(zoom.val() == 0) zoom.val(img.width()/img.data("width"));
			zoom.val(zoom.val()*0.9)
			img.height(img.data("height")*zoom.val());
			img.width(img.data("width")*zoom.val());
			$(this).closest(".screen").addClass("modified");
			return false;
		});
		//
		$(".pimage .image").mousedown(function(evt) {
			movingImage = $(this);
			movingStart = [evt.pageX,evt.pageY];
			console.log(movingStart);
			console.log(movingImage.offset());
			return false;
		});
		$(".pimage .image, .pimage").mousemove(function(evt) {
			if(movingImage != false) {
				var parent = movingImage.parent();
				var curPos = [evt.pageX,evt.pageY];
				var imgPos = {
					top: movingImage.offset().top-parent.offset().top,
					left:movingImage.offset().left-parent.offset().left,
				};
				var deltaX = curPos[0]-movingStart[0];
				var deltaY = curPos[1]-movingStart[1];
				var newPosX = Math.floor(imgPos.left*4.8+deltaX*4.8);
				var newPosY = Math.floor(imgPos.top*4.8+deltaY*4.8);
				// empêcher de sortir à gauche
				newPosX = Math.max(0, newPosX);
				newPosY = Math.max(0, newPosY);
				// empêcher de sortir à droite
				var fmw = Math.floor(fw-movingImage.width());
				var fmh = Math.floor(fh-movingImage.height());
				newPosX = Math.min(fmw, newPosX);
				newPosY = Math.min(fmh, newPosY);
				movingImage.css({
					left: newPosX+"px",
					top:  newPosY+"px",
					bottom:"auto",
					right:"auto",
				});
				movingStart = curPos;
				return false;
			}
		});
		function exitMove(evt) {
			console.log(evt);
			if(movingImage) {
				movingImage.closest(".screen").addClass("modified");
			}
			movingImage = false;
		}
		$("body").on("mouseup",exitMove);
	});
	</script>
	<script type="text/javascript" src="module_scoreboard.js"></script>
</head>
<body>
<div id="contenu">
<!--
	liste des écrans (images qu'on peut inclure dans son stream)
	- affiche l'image actuelle
	- permet de l'enlever (une tite croix)
	- permet de choisir une nouvelle image et prévisualiser
	- permet de valider la nouvelle image
-->
	<div id="ecrans">
<?php 
for($index=1; $index<=$Nscreens; $index++) {
	$imageurl = $prefs->screenFile($index);
	$imgPos = $prefs->screenPos($index);
	$base_lien = dirname($thisurl);
	$lien = $base_lien ."/?screen=".$index;
	if($imageurl != "" && file_exists($imageurl)) {
		$sizes = getimagesize($imageurl);
		$w = $sizes[0];
		$h = $sizes[1];
		$imageurl = $imageurl."?yo=".time()."x".$index;
	} else {
		$w = 0;
		$h = 0;
	}
	?>
	<div class='screen screen<?=$index?>'>
		<form action="<?=$thisurl?>" name="screens" method="POST">
		<p><a href="<?=$lien?>" target="_BLANK">Écran <?=$index?></a> <input type="text" class="lien" name="lien" value="<?=$lien?>" readonly/></p>
		<div class="pimage"><img class="image" data-width="<?=$w?>" data-height="<?=$h?>" data-top="<?=$imgPos[1]?>" data-left="<?=$imgPos[0]?>" data-zoom="<?=$imgPos[2]?>" src="<?=$imageurl?>"/>
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
	</div>
<!--
	affichage d'un strawpoll
-->
<?php disp_strawpoll($thisurl); ?>
<!--
	interface de comptage de points
	- ajouter un participant
	- liste avec un bouton + ou -
	- corriger un nom / enlever un participant
	- filtre dynamique par les noms
	- historique des points (pour le travail collaboratif)
-->
<?php disp_scoreboard($thisurl); ?>
<!--
	interface pour ajouter une image
	- depuis un fichier local
	- depuis le lien d'une image
	- depuis une page imgur ? (et compagnie)
-->
<?php disp_upload($thisurl); ?>
<!--
	liste des images disponibles avec un bouton pour les effacer (en cas d'erreur)
	bouton pour effacer toutes les images (pas celles en cours) à la fin du stream
-->
	<div id="sources">
<?php
$sources = array();
foreach(glob($sources_glob) as $source) {
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
		$imageurl = $imageurl."?yo=".time()."x".$index;
	} else {
		continue;
	}
	?><div class='source'>
		<form action="<?=$thisurl?>" name="sources" method="POST">
		<div class="pimage"><img class="image" data-width="<?=$w?>" data-height="<?=$h?>" src="<?=$imageurl?>"/>
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
</div>
</body>
</html>
