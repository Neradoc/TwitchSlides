<?php
// afficher les erreurs dans la gestion si possible
// (pas sur le stream)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include_once("head.php");
include_once("prefs.php");

$prefs = new PrefsManager();

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
	<title>Les écrans de realmyop</title>
	<link rel='stylesheet' href='cjs/gestion.css.php' type='text/css' />
	<script type="text/javascript" src="cjs/jquery2.js"></script>
	<script type="text/javascript" src="cjs/module_screens.js"></script>
	<script type="text/javascript" src="cjs/module_scoreboard.js"></script>
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
</body>
</html>
