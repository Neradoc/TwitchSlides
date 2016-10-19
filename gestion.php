<?php
include("head.php");

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

function exit_redirect() {
	#print("<pre>");
	#print_r($_POST);
	#print('<a href="'.$_SERVER['SCRIPT_URI'].'">'.$_SERVER['SCRIPT_URI'].'</a>');
	header('Location: '.$_SERVER['SCRIPT_URI']);
	exit();
}

function effacer_screen($screen) {
	global $image_exts,$image_format;
	foreach($image_exts as $ext) {
		$file = sprintf($image_format,$screen,$ext);
		if(file_exists($file)) {
			unlink($file);
		}
	}
}

if(isset($_POST["effacer_screen"])) {
	$screen = $_POST["effacer_screen"];
	effacer_screen($screen);
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

if(isset($_FILES["upload_fichier"])||isset($_POST["upload_url"])) {
	if(isset($_FILES["upload_fichier"])) {
		$file = $_FILES["upload_fichier"];
		if(preg_match('`image/(jpeg|png)$`i',$file['type'])) {
			$ext = pathinfo($file['name'],PATHINFO_EXTENSION);
			$filename = "sources/image_".uniqid().".".$ext;
			move_uploaded_file($file['tmp_name'],$filename);
		}
	}
	if(isset($_POST["upload_url"])) {
		// TOUDOU
	}
	exit_redirect();
}

if(isset($_POST['assign_source'])) {
	$screen = intval($_POST['assign_source']);
	if($screen>0) {
		$source = $_POST['source_file'];
		$source = basename($source);
		$source = "sources/".$source;
		if(file_exists($source)) {
			$ext = pathinfo($source,PATHINFO_EXTENSION);
			$screen_cible = sprintf($image_format,$screen,$ext);
			effacer_screen($screen);
			copy($source,$screen_cible);
		}
	}
	exit_redirect();
}

if(!empty($_POST) || !empty($_FILES)) {
	exit_redirect();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
	<meta charset="utf-8" />
	<title></title>
	<style type="text/css" title="text/css">
	p { padding: 2px; margin:0px; }
	.screen,
	.source {
		position: relative;
		float:left;
		padding: 8px;
		margin: 8px;
		border: 2px solid #088;
		border-radius: 8px;
		width: 400px;
	}
	.screen {
		border-color:#088;
		height: 240px;
	}
	.source {
		border-color:#880;
		height: 214px;
	}
	.screen .image,
	.source .image {
		max-width: 380px;
		max-height: 160px;
	}
	.pimage {
		vertical-align: middle;
		text-align: center;
		height: 160px;
		padding: 8px;
		background-image: url("damier.png");
	}
	.upload_fichier {
	}
	.upload_url {
		width: 300px;
	}
	.source .assign option:first-child {
		color: #888;
	}
	.btns button,
	.btns select {
		font-size: 120%;
		border-radius: 10px;
		background: transparent;
		border-color: #88F;
		cursor:pointer;
	}
	.btns button:hover {
		background:black;
		color:white;
	}
	hr { clear:both; }
	</style>
	<script type="text/javascript" src="jquery2.js"></script>
	<script type="text/javascript" language="javascript" charset="utf-8">
	$(function() {
		$(".assign").change(function() {
			$(this).closest("form").submit();
		});
		$(".lien").click(function() {
			$(this).select();
		});
	});
	</script>
</head>
<body>
<div id="contenu">
	<div id="ecrans">
<?php /*
	liste des écrans (images qu'on peut inclure dans son stream)
	- affiche l'image actuelle
	- permet de l'enlever (une tite croix)
	- permet de choisir une nouvelle image et prévisualiser
	- permet de valider la nouvelle image
*/
$screenImages = array();
for($screen=1; $screen<=$Nscreens; $screen++) {
	$screenImages[$screen] = false;
	foreach($image_exts as $ext) {
		$file = sprintf($image_format,$screen,$ext);
		if(file_exists($file)) {
			$screenImages[$screen] = $file;
			break;
		}
	}
}
foreach($screenImages as $index => $im) {
	$base_lien = dirname($_SERVER['SCRIPT_URI']);
	$lien = $base_lien."/?screen=".$index."&width=0&height=0&align=left";
	$imageurl = $im;
	if($imageurl != "") $imageurl = $im."?yo=".time()."x".$index;
	?>
	<div class='screen'>
		<form action="<?=$_SERVER['SCRIPT_URI']?>" name="screens" method="POST">
		<p>Écran <?=$index?> <input type="text" class="lien" name="lien" value="<?=$lien?>"/></p>
		<p class="pimage"><img class="image" src="<?=$imageurl?>"/></p>
		<div class="btns">
			<button class="effacer" name="effacer_screen" value="<?=$index?>">Effacer</button>
			<!-- <button class="activer" names="activer_screen" value="<?=$index?>">Activer (tweet)</button> -->
		</div>
		</form>
	</div><?
}
?>
	</div>
	<hr/>
	<div id="upload">
<?php /*
	interface pour ajouter une image
	- depuis un fichier local
	- depuis le lien d'une image
	- depuis une page imgur ? (et compagnie)
*/ ?>
	<form action="<?=$_SERVER['SCRIPT_URI']?>" name="upload" method="POST" enctype="multipart/form-data">
		<b>Ajouter une image</b><br/>
		Locale: <input type="file" name="upload_fichier" class="upload_fichier">
		<!-- Par une URL: <input type="text" name="upload_url" class="upload_url"> -->
		<button>Envoyer</button>
	</form>
	</div>
	<hr/>
	<div id="sources">
<?php
/*
	liste des images disponibles avec un bouton pour les effacer (en cas d'erreur)
	bouton pour effacer toutes les images (pas celles en cours) à la fin du stream
*/
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
foreach($sources as $info) {
	$source = $info['file'];
	$name = basename($source);
	?><div class='source'>
		<form action="<?=$_SERVER['SCRIPT_URI']?>" name="sources" method="POST">
		<input type="hidden" name="source_file" value="<?=$name?>"/>
		<p class="pimage"><img class="image" src="<?=$source?>"/></p>
		<div class="btns">
			<button class="effacer" name="effacer_source" value="<?=$name?>">Effacer</button>
			<select class="assign" name="assign_source">
				<option value="0">Envoyer sur un écran</option>
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
