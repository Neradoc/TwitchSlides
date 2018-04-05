<?php
include("head.php");

if(isset($_POST['scoreboard_reset'])) {
	$prefs->scores = [];
	$prefs->save();
	exit_redirect();
}

if(isset($_POST['messages_twitter']) && is_array($_POST['messages_twitter'])) {
	$messages = $_POST['messages_twitter'];
	$messages = array_filter($messages);
	$prefs->twitterMessages = $messages;
}

if(isset($_POST['url_miniature_stream'])) {
	if($_POST['url_miniature_stream'] == "" || $_POST['url_miniature_stream'] == $url_miniature_stream) {
		$prefs->del("url_miniature_stream");
	} else {
		$prefs->set("url_miniature_stream",$_POST['url_miniature_stream']);
	}
}

if(isset($_POST['categorie_name'])) {
	$names = $_POST['categorie_name'];
	$images = $_POST['categorie_image'];
	$categories = [];
	for($i=0; $i<count($names); $i++) {
		if(trim($names[$i]) != "") {
			$categories[$names[$i]] = $images[$i];
		}
	}
	$prefs->setCategories($categories);
}

if(!empty($_POST)) {
	$prefs->save();
	exit_redirect();
}

?><!DOCTYPE html>
<html lang="fr">
<head>
	<meta charset="utf-8" />
	<meta name="viewport" content="width=400">
	<title>Configuration: <?= $htmlTitleGestion ?></title>
	<script type="text/javascript" src="cjs/jquery2.js"></script>
	<script type="text/javascript" src="cjs/jquery.elastic.js"></script>
	<link rel="stylesheet" type="text/css" href="cjs/tooltipster/dist/css/tooltipster.bundle.min.css" />
	<script type="text/javascript" src="cjs/tooltipster/dist/js/tooltipster.bundle.min.js"></script>
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
	
	.config_nouvelle_categorie,
	.config_nouveau_message_twitter {
		width: 572px;
		font-size: 100%;
		border: 2px solid blue;
		background: white;
		padding: 8px;
		border-radius: 8px;
	}

	.config_nouvelle_categorie:hover,
	.config_nouveau_message_twitter:hover { background: #DDF; }
	.config_nouvelle_categorie:active,
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
	
	
	.categorie_line .categorie_image_bloc {
		display: inline-block;
		width: 32px;
	}
	.categorie_line .categorie_image {
		max-width: 32px;
		max-height: 32px;
	}
	.categorie_lineX .inputTitle,
	.categorie_line input {
		display: inline-block;
		font-size: 100%;
		width: 200px;
	}
	.categorie_line .config_retirer_categorie {
		border: 2px solid red;
		border-radius: 8px;
		background: #800;
		color: white;
	}
	.categorie_line0 { display: none; }
	.categorie_lineX input {
		border-color: transparent;
	}
	.categorie_lineX .nombre_image_par_categorie {
		width: 4em;
	}
	.ajout_categorie {
		margin: 8px 0px;
	}
	.nombre_image_par_categorie {
		display: inline-block;
		min-width: 2em;
		text-align: right;
		margin-right: 4px;
	}
	
	@media only screen and (max-device-width: 480px) {
		#contenu { width: 400px; }
		.message_twitter textarea { width: 320px; }
		.message_twitter button { width: 50px; }
		.config_nouveau_message_twitter { width: 390px; }
		input.url_miniature_stream { width: 375px; }
		.url_miniature_img { width: 375px; }
	}
	
	</style>
	<script type="text/javascript" language="javascript" charset="utf-8">
	$(function() {
		$("textarea").elastic();
		$(document).on("click",".config_retirer_message_twitter",function() {
			$(this).closest(".message_twitter").remove();
			return false;
		});
		$(".config_nouveau_message_twitter").on("click",function() {
			$(".message_twitter0")
				.clone()
				.removeClass("message_twitter0")
				.appendTo(".liste_messages_twitter");
			return false;
		});
		//
		$(document).on("click",".config_retirer_categorie",function() {
			$(this).closest(".categorie_line").remove();
			return false;
		});
		$(".config_nouvelle_categorie").on("click",function() {
			$(".categorie_line0")
				.clone()
				.removeClass("categorie_line0")
				.appendTo(".liste_categories");
			return false;
		});
		//
 		$(document).on("change",".categorie_image_url",function() {
 			var url = $(this).val();
 			console.log(url);
 			$(this).siblings(".categorie_image_bloc")
 				.find("img").attr("src",url);
 		});
 		//
		var tooltipOptions = {
			animationDuration: 0,
			delay: [500,0],
			distance: 0,
		}
		$(".tooltiper").tooltipster(tooltipOptions);
	});
	</script>
</head>
<body>
<div id="menu"><a href="gestion">Gestion</a><a class="ici" href="config">Config</a><a href="sources">Sources</a></div>
<div id="contenu">
<h2>Actions</h2>
<form action="<?=$thisurl?>" name="config" method="POST">

<p class="config_scoreboard_reset_line">Effacer tous les scores (attention: irréversible) <button class="config_scoreboard_reset" name="scoreboard_reset" value="1">EFFACER LES SCORES</button></p>

</form>

<hr/>

<form action="<?=$thisurl?>" name="config" method="POST">
<h2>Configuration du bidule</h2>

<p>
<?php
$url = $url_miniature_stream;
?>
	Image de fond des écrans<br/>
	<input class="url_miniature_stream" type="url" name="url_miniature_stream" value="<?=$url?>"/><br/>
	<img class="url_miniature_img" src="<?=$url?>"/>
</p>

<h3>Liste des messages twitter par défaut</h3>
<div>
	<div class="liste_messages_twitter">
		<div class="message_twitter message_twitter0"><textarea name="messages_twitter[]"></textarea><button class="config_retirer_message_twitter" name="config_retirer_message_twitter" value="">Retirer</button></div>
		<?php
		$prefMess = array_filter($twitterMessages);
		foreach($prefMess as $message) {
			?><div class="message_twitter"><textarea name="messages_twitter[]"><?=strip_tags($message)?></textarea><button class="config_retirer_message_twitter" name="config_retirer_message_twitter" value="">Retirer</button></div><?
		}
		?>
	</div>
	<div class="ajout_message"><button class="config_nouveau_message_twitter" name="nouveau" value="1">Nouveau</button></div>
</div>

<h3>Liste des catégories des images sources <img class="tooltiper" src="cjs/img/bouton_question.png" onclick="$('.cat_info').toggle();" title="Informations"></h3>
<p class="cat_info" style="display:none;">L'image est soit un lien relatif au dossier des écrans, soit un lien absolu (c'est à dire commençant par http). Le nom de la catégorie peut être une phrase. Il n'est pas possible de changer le nom d'une catégorie une fois créée (pour le moment). Les modifications ne sont validées qu'avec le bouton tout en bas.</p>
<div>
	<div class="liste_categories">
		<div class="categorie_line categorie_lineX">
			<span class="categorie_image_bloc"><img class="categorie_image" src="cjs/img/vide.png"/></span>
			<span class="nombre_image_par_categorie"></span>
			<span class="inputTitle">Nom de la catégorie</span>
			<span class="inputTitle">Lien de l'image</span>
		</div>
		<div class="categorie_line categorie_line0">
			<span class="categorie_image_bloc"><img class="categorie_image" src="cjs/cats/nogrp.png"/></span>
			<span class="nombre_image_par_categorie">0</span>
			<input name="categorie_name[]" value=""/>
			<input class="categorie_image_url" name="categorie_image[]" value="cjs/cats/nogrp.png"/>
			<button class="config_retirer_categorie" name="config_retirer_categorie" value="">Retirer</button>
		</div>
		<?php
		$categories = $prefs->categories();
		$categoriesSize = $prefs->categoriesSize();
		foreach($categories as $categorie => $image) {
			if(isset($categoriesSize[$categorie])) {
				$size = $categoriesSize[$categorie];
			} else {
				$size = 0;
			}
			?><div class="categorie_line">
				<span class="categorie_image_bloc"><img class="categorie_image" src="<?=$image?>"/></span>
				<span class="nombre_image_par_categorie"><?=$size?></span>
				<input type="hidden" name="old_categorie_name[]" value="<?=
				strip_tags($categorie)?>"/>
				<input type="text" name="categorie_name[]" value="<?=
				strip_tags($categorie)?>" readonly="readonly"/>
				<input type="text" class="categorie_image_url" name="categorie_image[]" value="<?=strip_tags($image)?>"/>
				<button class="config_retirer_categorie" name="config_retirer_categorie" value="">Retirer</button>
			</div><?
		}
		?>
	</div>
	<div class="ajout_categorie"><button class="config_nouvelle_categorie" name="config_nouvelle_categorie" value="1">Nouveau</button></div>
</div>

<div><input class="valider" type="submit" value="Valider tout ça" /></div>
</form>
</div>
</body>
</html>
