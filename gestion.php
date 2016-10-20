<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include("head.php");
include("prefs.php");
include("twitter.php");

$prefs = new PrefsManager();

function exit_redirect() {
	global $thisurl;
	#print("<pre>");
	#print_r($_POST);
	#print('<a href="'.$thisurl.'">'.$thisurl.'</a>');
	header('Location: '.$thisurl);
	//header("Refresh:0");
	exit();
}

function is_image($file) {
	if(!file_exists($file)) { return false; }
	$mime = mime_content_type($file);
	if(preg_match('`image/(jpeg|png)`i',$mime)) return true;
	return false;
}

function screenFile($screenNum) {
	global $prefs;
	if(isset($prefs->screens[$screenNum])) {
		if($prefs->screens[$screenNum] != "") {
			return "images/".$prefs->screens[$screenNum];
		}
	}
	return "";
}

function effacer_screen($screen) {
	global $prefs;
	$file = screenFile($screen);
	if($file != "") {
		$prefs->screens[$screen] = "";
		$prefs->save();
		if(file_exists($file)) {
			unlink($file);
		}
	}
}

$poll_embed = "";
$poll_page = $prefs->get("strawpoll","");
if(preg_match('`(\d\d+)`',$poll_page,$m)) {
	$numpoll = $m[1];
	$poll_embed = "http://www.strawpoll.me/embed_1/$numpoll/r";
}

if(isset($_POST["strawpoll_lien"])) {
	$poll = $_POST["strawpoll_lien"];
	$prefs->set("strawpoll",$poll);
	$prefs->save();
	exit_redirect();
}

if(isset($_POST["effacer_screen"])) {
	$screen = intval($_POST["effacer_screen"]);
	effacer_screen($screen);
	exit_redirect();
}

if(isset($_POST["twitter_screen"])) {
	$screen = intval($_POST["twitter_screen"]);
	$file = screenFile($screen);
	if($file && file_exists($file)) {
		$urlImage = dirname($thisurl).$file;
		$urlImage = "http://realmyop.fr/ecrans/".$file; ###################
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

if(isset($_FILES["upload_fichier"])||isset($_POST["upload_url"])) {
	if(isset($_FILES["upload_fichier"])) {
		$file = $_FILES["upload_fichier"];
		if(is_image($file['tmp_name'])) {
			$ext = pathinfo($file['name'],PATHINFO_EXTENSION);
			$filename = "sources/image_".uniqid().".".$ext;
			move_uploaded_file($file['tmp_name'],$filename);
		}
	}
	if(isset($_POST["upload_url"]) && $_POST["upload_url"] != "") {
		$tmp_file = tempnam("/tmp", "twitch_slides");
		if($tmp_file) {
			$data = file_get_contents($_POST["upload_url"]);
			file_put_contents($tmp_file,$data);
			switch(mime_content_type($tmp_file)) {
			case "image/jpeg":
				$ext = "jpg";
				break;
			case "image/png":
				$ext = "png";
				break;
			default:
				$ext = "";
			}
			if($ext) {
				mime_content_type($tmp_file);
				$filename = "sources/image_".uniqid().".".$ext;
				rename($tmp_file,$filename);
				chmod($filename,0777);
			} else {
				unlink($tmp_file);
			}
		}
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
			$screen_cible = sprintf($image_format,md5($source),$ext);
			effacer_screen($screen);
			copy($source,$screen_cible);
			$prefs->screens[$screen] = basename($screen_cible);
			$prefs->save();
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
	<link rel="shortcut icon" href="favirm.png" />
	<meta name="viewport" content="width=412">
	<title>Les écrans de realmyop</title>
	<style type="text/css" title="text/css">
	body { padding: 0px; margin:0px; }
	p { padding: 2px; margin:0px; }
	.screen,
	.source,
	#strawpoll {
		position: relative;
		float:left;
		padding: 8px;
		margin: 8px;
		border-width: 2px;
		border-style: solid;
		border-radius: 8px;
		width: 376px;
	}
	.screen {
		border-color:#080;
		height: 240px;
	}
	.source {
		border-color:#008;
		height: 214px;
	}
	#strawpoll {
		border-color:#080;
		height: 240px;
	}
	#upload {
		position: relative;
		padding: 8px;
		margin: 8px;
		clear:both;
		border-width: 2px 0px 2px;
		border-style: solid;
		border-color:#FF0;
	}
	.pimage {
		text-align: center;
		height: 160px;
		padding: 8px;
		background-image: url("damier.png");
	}
	.screen .image,
	.source .image {
		max-width: 360px;
		max-height: 160px;
	}
	.upload_fichier {
		font-size: 100%;
	}
	.upload_url {
		width: 280px;
	}
	.strawpoll_lien {
		width: 360px;
	}
	.stropaul_frame {
		width:752px;/*376px;*/
		height:400px;
		border:0;
		transform: scale(0.5,0.5);
		transform-origin: top left;
	}
	#upload .upload_btn {
		font-size: 100%;
		margin-left:310px;
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
		background:#000;
		color:white;
	}
	.btns .twitter {
		border-color: green;
	}
	.btns .effacer {
		border-color: red;
	}
	.btns button.effacer:hover {
		background:#800;
		color:white;
	}
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
<!--
	liste des écrans (images qu'on peut inclure dans son stream)
	- affiche l'image actuelle
	- permet de l'enlever (une tite croix)
	- permet de choisir une nouvelle image et prévisualiser
	- permet de valider la nouvelle image
-->
	<div id="ecrans">
<?php 
$screenImages = array();
for($screen=1; $screen<=$Nscreens; $screen++) {
	$screenImages[$screen] = false;
	$file = screenFile($screen);
	if($file && file_exists($file)) {
		$screenImages[$screen] = $file;
		break;
	}
}
foreach($screenImages as $index => $im) {
	$base_lien = dirname($thisurl);
	$lien = $base_lien."/?screen=".$index."&width=0&height=0&align=left";
	$imageurl = $im;
	if($imageurl != "") $imageurl = $im."?yo=".time()."x".$index;
	?>
	<div class='screen screen<?=$index?>'>
		<form action="<?=$thisurl?>" name="screens" method="POST">
		<p>Écran <?=$index?> <input type="text" class="lien" name="lien" value="<?=$lien?>"/></p>
		<p class="pimage"><img class="image" src="<?=$imageurl?>"/></p>
		<div class="btns">
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
	<div id="strawpoll">
	<form action="<?=$thisurl?>" name="strawpoll" method="POST" enctype="multipart/form-data">
		Strawpoll: <input type="text" name="strawpoll_lien" class="strawpoll_lien" value="<?=$poll_page?>"/><br/>
		<iframe class="stropaul_frame" src="<?=$poll_embed?>">Loading poll...</iframe>
	</form>
	</div>
<!--
	interface de comptage de points
	- ajouter un participant
	- liste avec un bouton + ou -
	- corriger un nom / enlever un participant
	- filtre dynamique par les noms
	- historique des points (pour le travail collaboratif)
-->
	<div id="scoreboard">
	<?php 
	//print(time());
	?>
	</div>
<!--
	interface pour ajouter une image
	- depuis un fichier local
	- depuis le lien d'une image
	- depuis une page imgur ? (et compagnie)
-->
	<div id="upload">
	<form action="<?=$thisurl?>" name="upload" method="POST" enctype="multipart/form-data">
		<b>Ajouter une image</b><br/>
		Locale&nbsp;: <input type="file" name="upload_fichier" class="upload_fichier"><br/>
		Par une URL&nbsp;: <input type="text" name="upload_url" class="upload_url"><br/>
		<button class="upload_btn">Envoyer</button>
	</form>
	</div>
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
		<form action="<?=$thisurl?>" name="sources" method="POST">
		<input type="hidden" name="source_file" value="<?=$name?>"/>
		<p class="pimage"><img class="image" src="<?=$source?>"/></p>
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
