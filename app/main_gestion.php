<?php
// afficher les erreurs dans la gestion si possible
// (pas sur le stream)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include_once("head.php");
include_once("module_menu.php");
include_once("module_slide.php");
include_once("module_livevisu.php");
include_once("module_strawpoll.php");
include_once("module_upload.php");
include_once("module_scoreboard.php");
include_once("module_twitter.php");
include_once("module_screens.php");

if(!empty($_POST) || !empty($_FILES)) {
	exit_redirect();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
	<meta charset="utf-8" />
	<link rel="shortcut icon" href="favicon.png" />
	<meta name="viewport" content="width=420">
	<title><?= $htmlTitleGestion ?></title>
	<link rel='stylesheet' href='cjs/gestion.css.php' type='text/css' />
	<script type="text/javascript" src="cjs/jquery2.js"></script>
	<script type="text/javascript" src="cjs/jquery.elastic.js"></script>
	<link rel="stylesheet" type="text/css" href="cjs/tooltipster/dist/css/tooltipster.bundle.min.css" />
	<script type="text/javascript" src="cjs/tooltipster/dist/js/tooltipster.bundle.min.js"></script>
	<script type="text/javascript" src="cjs/gestion.js"></script>
	<script type="text/javascript" src="cjs/module_menu.js"></script>
	<script type="text/javascript" src="cjs/module_screens.js"></script>
	<script type="text/javascript" src="cjs/module_twitter.js"></script>
	<script type="text/javascript" src="cjs/module_scoreboard.js"></script>
	<script type="text/javascript" src="cjs/module_slide.js"></script>
	<script type="text/javascript" src="cjs/module_livevisu.js"></script>
	<script type="text/javascript" src="cjs/module_upload.js"></script>
</head>
<body>
<input id="time_origin" type="hidden" name="time_origin" value="<?=time()?>"/>
<input id="calc_score" type="hidden" name="calc_score" value="0" data-calcul="<?=$calc_score?>"/>
<!--
	menu
-->
<?php disp_menu($thisurl); ?>
<div id="contenu" class="main_gestion">
<!--
	prévisu du slide
-->
<?php disp_slide($thisurl); ?>
<!--
	visu du slide tel qu'il est sur le stream
-->
<?php disp_livevisu($thisurl); ?>
<!--
	liste des écrans (images qu'on peut inclure dans son stream)
	- affiche l'image actuelle
	- permet de l'enlever (une tite croix)
	- permet de choisir une nouvelle image et prévisualiser
	- permet de valider la nouvelle image
-->
<?php disp_screens($thisurl); ?>
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
<?php disp_sources($thisurl); ?>
</div>
<div id="black_block" style="display:none;"></div>
<!--
	fenêtre twitter pour la validation
-->
<?php disp_twitter($thisurl); ?>
</body>
</html>
