<?php
include("head.php");

?><!DOCTYPE html>
<html lang="fr">
<head>
	<meta charset="utf-8" />
	<meta name="viewport" content="width=400">
	<title></title>
	<style type="text/css" title="text/css">
	body {
		padding:0px;
		margin:0px;
		overflow: auto;
		background: #ADD8E6;
	}
	#contenu {
		border-width: 4px 4px 4px;
		border-style: solid;
		border-color: #60A0B6;
		width: 640px;
		margin: 0px auto 0px;
		padding: 0px 20px 20px;
		background: white;
		overflow: auto;
	}
	#menu {
		width: 640px;
		margin: 0px auto 0px;
		padding: 2px 8px 0px;
		overflow: auto;
	}
	#menu a
	{
		float: left;
		padding: 4px 16px;
		margin: 0px 2px;
		color: black;
		background: white;
		border-width: 2px 2px 0px 2px;
		border-color: #60A0B6;
		border-style: solid;
		border-radius: 8px 8px 0px 0px;
		text-decoration: none;
	}
	#menu a.ici {
		background: #60A0B6;
	}
	
	button {
		background: white;
	}

	.message_twitter {
		padding: 4px 0px;
		vertical-align: bottom;
		overflow: auto;
	}
	.message_twitter textarea,
	.message_twitter button {
		float: left;
	}
	.message_twitter textarea {
		font-size: 100%;
		padding: 4px;
		margin: 0px;
		display: inline-block;
		width: 564px;
		height: 4em;
	}
	.config_retirer_message_twitter {
		display: inline-block;
		width: 56px;
		height: 50px;
		padding: 0px;
		margin: 0px;
		margin-left: 8px;
		border: 2px solid red;
		border-radius: 8px;
		background: #800;
		color: white;
	}
	.config_scoreboard_reset_line {
		overflow: auto;
	}
	.config_scoreboard_reset {
		float: right;
		border: 2px solid red;
		border-radius: 8px;
		background: #800;
		color: white;
		padding: 4px 20px;
	}
	.config_scoreboard_reset:hover {
		background: red;
	}
	
	.message_twitter button:hover { background: red; }
	.message_twitter button:active { background: #400; color: white; }
	.message_twitter0 { display:none; }
	
	.ajout_message { overflow: auto; }
	.config_nouveau_message_twitter {
		width: 572px;
		font-size: 100%;
		border: 2px solid blue;
		background: white;
		padding: 8px;
		border-radius: 8px;
	}

	.config_nouveau_message_twitter:hover { background: #DDF; }
	.config_nouveau_message_twitter:active { background: #000; color:white; }
	
	button { cursor: pointer; }
	
	input.url_miniature_stream {
		width: 624px;
		padding: 4px;
		font-size: 90%;
		border: 2px solid #5B3693;
		border-radius: 4px;
	}
	.url_miniature_img {
		display:block;
		width:400px;
		max-height:300px;
		margin: 8px auto;
		border: 4px dashed #888;
	}
	.valider {
		float:right;
		font-size: 100%;
		border: 2px solid green;
		background: white;
		padding: 8px;
		border-radius: 8px;
	}
	.valider:hover { background: #8D8; }
	.valider:active { background: #000; color:white; }

	.parity_doublons0 { background: #DDDDFF; }
	.parity_doublons1 { background: #DDFFDD; }
	
	.cat_image {
		max-width: 32px;
		max-height: 32px;
	}
	</style>
	<script type="text/javascript" src="cjs/jquery2.js"></script>
	<script type="text/javascript" src="cjs/jquery.elastic.js"></script>
	<script type="text/javascript" language="javascript" charset="utf-8">
	$(function() {
		$("textarea").elastic();
	});
	</script>
</head>
<body>
<div id="menu"><a href="gestion">Gestion</a><a href="config">Config</a><a class="ici" href="sources">Sources</a></div>
<div id="contenu">
<h2>Nettoyer les sources</h2>
<?php
global $prefs;
$starsList = [];
$sizeStars = 0;
$sizeReste = 0;
foreach(glob(SOURCES_GLOB) as $source) {
	if(file_exists($source)) {
		$file = basename($source);
		$size = filesize($source);
		$md5 = md5_file($source);
		$sources[] = array(
			'file' => $source,
			'date' => filemtime($source),
			'size' => $size,
			'md5' => $md5,
		);
		if(isset($prefs->stars[$file])) {
			if($prefs->stars[$file] === true) $prefs->stars[$file] = "star";
			if(!isset($starsList[$prefs->stars[$file]])) {
				$starsList[$prefs->stars[$file]] = ['size' => 0, 'num' => 0];
			}
			$starsList[$prefs->stars[$file]]['size'] += $size / 1024 / 1024;
			$starsList[$prefs->stars[$file]]['num'] += 1;
			$sizeStars += $size;
		} else {
			$sizeReste += $size;
		}
	}
}
$sizeStars = $sizeStars / 1024 / 1024;
$sizeReste = $sizeReste / 1024 / 1024;

usort($sources,function($a,$b) {
	return strcmp($a['md5'],$b['md5']);
});
$lesSources = $sources;
$numSources = count($sources);
?>
<div>
	<p>Total nombre d'images: <?=$numSources?></p>
	<p>Il y a pour: <?=sprintf("%.2f",$sizeStars)?> Mo d'images favorites.</p>
<?
$catImgs = $prefs->categories();
foreach($starsList as $cat => $info) {
	if(isset($catImgs[$cat])) {
		$image = $catImgs[$cat];
	} else {
		$image = "";
	}
	?><p><img class="cat_image" src="<?=$image?>"/> Taille totale: <?=sprintf("%.2f",$info['size'])?> Mo pour <b><?=$info['num']?></b> images "<?=$cat?>".</p><?
}
?>
	<p><img class="cat_image" src="cjs/cats/nogrp.png"/> Il y a pour: <?=sprintf("%.2f",$sizeReste)?> Mo d'images non favorites.</p>
</div>
<div><h3>Doublons</h3>
<pre>
<?
$parity_doublons = 0;
for($i=0; $i<count($sources);  $i++) {
	$doublons = [];
	for($j=1; $i+$j<count($sources); $j++) {
		if($sources[$i]['md5'] == $sources[$i+$j]['md5']) {
			$doublons[] = basename($sources[$i+$j]['file']);
		} else {
			break;
		}
	}
	if(count($doublons)>0) {
		$parity_doublons += 1;
		array_unshift($doublons,basename($sources[$i]['file']));
		print("<span class='parity_doublons".($parity_doublons%2)."'>"
			.join($doublons,"\n")."</span>\n");
		$i += count($doublons);
		continue;
	}
	// print($sources[$i]['file']."\n");
}
?>
</pre>
</div>

</div>
</body>
</html>
