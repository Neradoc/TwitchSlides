<?php
include("head.php");

if(isset($_POST['messages_twitter']) && is_array($_POST['messages_twitter'])) {
	$messages = $_POST['messages_twitter'];
	$messages = array_filter($messages);
	$prefs->twitterMessages = $messages;
}

if(isset($_POST['Nscreens'])) {
	$N = intval($_POST['Nscreens']);
	if($N > 0 && $N != $Nscreens) {
		$prefs->set("Nscreens",$N);
	} else {
		$prefs->del("Nscreens");
	}
}

if(isset($_POST['url_miniature_stream'])) {
	if($_POST['url_miniature_stream'] == "" || $_POST['url_miniature_stream'] == $url_miniature_stream) {
		$prefs->del("url_miniature_stream");
	} else {
		$prefs->set("url_miniature_stream",$_POST['url_miniature_stream']);
	}
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
	.message_twitter button {
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
	
	button:hover { background: #CCC; }
	button:active { background: #444; color: white; }
	
	input.nscreens {
		width: 3em;
		text-align: right;
		font-size: 100%;
		border: 2px solid #DD6;
		border-radius: 4px;
	}
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
	
	@media only screen and (max-device-width: 480px) {
		#contenu { width: 400px; }
		.message_twitter textarea { width: 320px; }
		.message_twitter button { width: 50px; }
		.config_nouveau_message_twitter { width: 390px; }
		input.url_miniature_stream { width: 375px; }
		.url_miniature_img { width: 375px; }
	}
	
	</style>
	<script type="text/javascript" src="cjs/jquery2.js"></script>
	<script type="text/javascript" src="cjs/jquery.elastic.js"></script>
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
	});
	</script>
</head>
<body>
<div id="menu"><a href="gestion">Gestion</a><a class="ici" href="config">Config</a></div>
<div id="contenu">
<form action="<?=$thisurl?>" name="config" method="POST">
<h2>Configuration du bidule</h2>
<p>Liste des messages twitter par défaut</p>
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

<p>Nombre d'écrans (images) configurables <input class="nscreens" type="number" name="Nscreens" value="<?=$Nscreens?>"/></p>

<p>
<?php
$url = $url_miniature_stream;
?>
	Image de fond des écrans<br/>
	<input class="url_miniature_stream" type="url" name="url_miniature_stream" value="<?=$url?>"/><br/>
	<img class="url_miniature_img" src="<?=$url?>"/>
</p>
<div><input class="valider" type="submit" /></div>
</form>
</div>
</body>
</html>
